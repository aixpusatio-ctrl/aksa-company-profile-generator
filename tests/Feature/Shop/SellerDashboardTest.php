<?php

namespace Tests\Feature\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\User;
use App\Services\Shop\InventoryService;
use App\Services\Shop\ShopService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Seller "Online Shop" dashboard: every page renders and the main
 * create/update forms post the field names the controllers validate.
 */
class SellerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CompanyProfile $company;

    private string $base;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->user = $this->userWithPlan();
        $this->company = $this->companyFor($this->user, ['name' => 'Toko Uji Seller']);
        app(ShopService::class)->enable($this->company);
        $this->company->refresh();
        $this->base = '/dashboard/websites/'.$this->company->id.'/shop';
    }

    private function product(array $attributes = []): Product
    {
        return $this->company->shopProducts()->create($attributes + [
            'name' => 'Kursi Rotan', 'slug' => 'kursi-rotan-'.Str::random(4), 'sku' => 'KR-'.Str::random(4), 'price' => 250000,
            'stock' => 10, 'track_stock' => true, 'stock_status' => 'in_stock', 'status' => 'published',
        ]);
    }

    private function order(Product $product, int $quantity = 2): Order
    {
        $customer = Customer::query()->create(['company_profile_id' => $this->company->id, 'name' => 'Budi Pembeli', 'email' => 'budi@example.test', 'phone' => '08123456789']);
        $order = $this->company->orders()->create([
            'customer_id' => $customer->id, 'order_number' => 'ORD-'.Str::upper(Str::random(6)), 'access_token' => Str::random(40),
            'subtotal' => $product->price * $quantity, 'shipping_cost' => 20000, 'total' => $product->price * $quantity + 20000,
            'customer_name' => 'Budi Pembeli', 'customer_email' => 'budi@example.test', 'customer_phone' => '08123456789',
            'shipping_address' => ['address' => 'Jl. Mawar 1', 'city' => 'Bandung', 'postal_code' => '40111'],
            'shipping_method' => 'Pengiriman reguler', 'payment_method' => 'Transfer Bank',
        ]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku, 'price' => $product->price, 'quantity' => $quantity, 'subtotal' => $product->price * $quantity]);
        $order->histories()->create(['type' => 'order', 'status' => 'pending', 'note' => 'Pesanan dibuat']);
        app(InventoryService::class)->reserve($order->load('items.product'));

        return $order;
    }

    public function test_every_seller_page_renders(): void
    {
        $product = $this->product();
        $order = $this->order($product);
        $category = $this->company->productCategories()->create(['name' => 'Furnitur', 'slug' => 'furnitur', 'status' => 'active']);
        $this->company->productCategories()->create(['name' => 'Kursi', 'slug' => 'kursi', 'status' => 'active', 'parent_id' => $category->id]);
        $this->company->coupons()->create(['code' => 'HEMAT10', 'type' => 'percentage', 'value' => 10, 'status' => 'active']);
        $this->company->taxes()->create(['name' => 'PPN', 'rate' => 11, 'inclusive' => false, 'is_active' => true]);
        $this->company->productReviews()->create(['product_id' => $product->id, 'name' => 'Rina', 'email' => 'rina@example.test', 'rating' => 4, 'body' => 'Kursinya nyaman sekali', 'status' => 'pending']);

        $this->actingAs($this->user)->get('/dashboard/shop')->assertRedirect(route('websites.shop.overview', $this->company));

        $pages = [
            '' => 'Penjualan 30 hari terakhir',
            '/settings' => 'Section halaman Shop',
            '/products' => 'Kursi Rotan',
            '/products/create' => 'Opsi & varian',
            '/products/'.$product->id.'/edit' => 'Kursi Rotan',
            '/categories' => 'Furnitur',
            '/orders' => $order->order_number,
            '/orders/'.$order->order_number => 'Budi Pembeli',
            '/orders/'.$order->order_number.'/invoice' => 'INVOICE',
            '/customers' => 'Budi Pembeli',
            '/customers/'.$order->customer_id => 'Riwayat pesanan',
            '/coupons' => 'HEMAT10',
            '/inventory' => 'Kursi Rotan',
            '/inventory?filter=low' => 'Stok produk',
            '/reviews' => 'Kursinya nyaman sekali',
            '/shipping' => 'Pengiriman reguler',
            '/payments' => 'Transfer Bank',
            '/discounts' => 'PPN',
        ];

        foreach ($pages as $suffix => $text) {
            $this->actingAs($this->user)->get($this->base.$suffix)->assertOk()->assertSee($text, false);
        }

        // Nav entries in the app sidebar and website editor.
        $this->actingAs($this->user)->get($this->base)->assertSee('Online Shop')->assertSee('Lihat toko');
    }

    public function test_hub_lists_websites_when_user_has_several(): void
    {
        $this->companyFor($this->user, ['name' => 'Website Kedua']);

        $this->actingAs($this->user)->get('/dashboard/shop')->assertOk()->assertSee('Toko Uji Seller')->assertSee('Website Kedua')->assertSee('Aktifkan shop');
    }

    public function test_toggle_and_settings_and_sections(): void
    {
        $this->actingAs($this->user)->post($this->base.'/toggle')->assertRedirect();
        $this->assertFalse($this->company->fresh()->hasShop());
        $this->actingAs($this->user)->get($this->base)->assertOk()->assertSee('Aktifkan Online Shop');
        $this->actingAs($this->user)->post($this->base.'/toggle');
        $this->assertTrue($this->company->fresh()->hasShop());

        $this->actingAs($this->user)->put($this->base.'/settings', [
            'name' => 'Toko Baru', 'description' => 'Deskripsi', 'currency' => 'IDR', 'order_prefix' => 'tb', 'low_stock_threshold' => 3,
            'options' => ['cta_buy_now' => '1', 'min_order' => 50000, 'products_per_page' => 24, 'shop_title' => 'Belanja hemat'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $settings = $this->company->shopSetting()->first();
        $this->assertSame('TB', $settings->order_prefix);
        $this->assertTrue($settings->option('cta_buy_now'));
        $this->assertFalse($settings->option('cta_add_to_cart'));
        $this->assertSame('Belanja hemat', $settings->option('shop_title'));

        $this->actingAs($this->user)->putJson($this->base.'/sections', ['sections' => [['key' => 'sale', 'enabled' => true], ['key' => 'hero', 'enabled' => false]]])->assertOk();
        $this->assertSame('sale', $settings->fresh()->sectionList()[0]['key']);
    }

    public function test_create_and_update_product_with_variants(): void
    {
        $category = $this->company->productCategories()->create(['name' => 'Kaos', 'slug' => 'kaos', 'status' => 'active']);
        $tag = $this->company->productTags()->first();

        $this->actingAs($this->user)->post($this->base.'/products', [
            'name' => 'Kaos Polos', 'price' => 100000, 'sale_price' => 90000, 'compare_price' => 120000, 'cost_price' => 50000,
            'stock_status' => 'in_stock', 'status' => 'published', 'track_stock' => '1', 'featured' => '1', 'weight' => 200,
            'category_id' => $category->id, 'tags' => [$tag->id], 'description' => '<p>Bahan <strong>katun</strong></p><script>x</script>',
            'specifications' => [['label' => 'Bahan', 'value' => 'Katun'], ['label' => '', 'value' => '']],
            'cta' => ['whatsapp' => '0', 'buy_now' => 'default'],
            'has_variants' => '1',
            'options' => [['name' => 'Ukuran', 'values' => 'S, M'], ['name' => 'Warna', 'values' => 'Hitam, Putih 1.5']],
            'variants' => [
                md5('S / Hitam') => ['sku' => 'KP-S-HTM', 'price' => 100000, 'stock' => 5, 'is_active' => '1'],
                md5('M / Putih 1.5') => ['price' => 110000, 'stock' => 3, 'is_active' => '0'],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $product = $this->company->shopProducts()->where('name', 'Kaos Polos')->firstOrFail();
        $this->assertCount(4, $product->variants);
        $this->assertSame(5, $product->variants->firstWhere('label', 'S / Hitam')->stock);
        $this->assertSame('KP-S-HTM', $product->variants->firstWhere('label', 'S / Hitam')->sku);
        $this->assertFalse($product->variants->firstWhere('label', 'M / Putih 1.5')->is_active);
        $this->assertSame(['whatsapp' => false], $product->cta);
        $this->assertStringNotContainsString('<script>', (string) $product->description);
        $this->assertCount(1, $product->specifications);
        $this->assertTrue($product->tags->contains($tag));

        $this->actingAs($this->user)->get($this->base.'/products/'.$product->id.'/edit')->assertOk()->assertSee('Ukuran')->assertSee('KP-S-HTM');

        // Update: drop variants (has_variants = 0) and change price.
        $this->actingAs($this->user)->put($this->base.'/products/'.$product->id, [
            'name' => 'Kaos Polos Premium', 'price' => 150000, 'stock_status' => 'in_stock', 'status' => 'draft', 'track_stock' => '0', 'has_variants' => '0',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $product->refresh();
        $this->assertSame('Kaos Polos Premium', $product->name);
        $this->assertCount(0, $product->variants);
        $this->assertFalse($product->track_stock);

        // Duplicate + delete.
        $this->actingAs($this->user)->post($this->base.'/products/'.$product->id.'/duplicate')->assertRedirect();
        $this->assertSame(2, $this->company->shopProducts()->count());
        $this->actingAs($this->user)->delete($this->base.'/products/'.$product->id)->assertRedirect();
        $this->assertSame(1, $this->company->shopProducts()->count());
    }

    public function test_categories_tags_coupons_shipping_payments_taxes_crud(): void
    {
        $as = $this->actingAs($this->user);

        $as->post($this->base.'/categories', ['name' => 'Dekorasi', 'status' => 'active'])->assertSessionHasNoErrors();
        $root = $this->company->productCategories()->where('name', 'Dekorasi')->firstOrFail();
        $as->post($this->base.'/categories', ['name' => 'Vas Bunga', 'status' => 'active', 'parent_id' => $root->id])->assertSessionHasNoErrors();
        $child = $this->company->productCategories()->where('name', 'Vas Bunga')->firstOrFail();
        $this->assertSame($root->id, $child->parent_id);
        $as->put($this->base.'/categories/'.$child->id, ['name' => 'Vas', 'status' => 'inactive', 'parent_id' => $root->id])->assertSessionHasNoErrors();
        $this->assertSame('inactive', $child->fresh()->status);

        $as->postJson($this->base.'/tags', ['name' => 'Limited', 'color' => 'rose'])->assertOk()->assertJsonPath('name', 'Limited');

        $as->post($this->base.'/coupons', ['code' => 'ongkir0', 'type' => 'free_shipping', 'status' => 'active'])->assertSessionHasNoErrors();
        $coupon = $this->company->coupons()->where('code', 'ONGKIR0')->firstOrFail();
        $as->put($this->base.'/coupons/'.$coupon->id, ['code' => 'DISKON15', 'type' => 'percentage', 'value' => 15, 'max_discount' => 50000, 'status' => 'active'])->assertSessionHasNoErrors();
        $this->assertSame('DISKON15', $coupon->fresh()->code);

        $as->post($this->base.'/shipping', ['type' => 'custom', 'name' => 'Kurir Lokal', 'cost' => 10000, 'per_kg' => 2000, 'cities' => "Jakarta=15000\nBandung=20000\ninvalid", 'is_active' => '1'])->assertSessionHasNoErrors();
        $shipping = $this->company->shippingMethods()->where('name', 'Kurir Lokal')->firstOrFail();
        $this->assertEquals(['Jakarta' => 15000, 'Bandung' => 20000], $shipping->config['cities']);
        $as->put($this->base.'/shipping/'.$shipping->id, ['type' => 'free', 'name' => 'Gratis Ongkir', 'min_order' => 300000])->assertSessionHasNoErrors();
        $this->assertFalse($shipping->fresh()->is_active);

        $as->post($this->base.'/payments', ['type' => 'bank_transfer', 'name' => 'Transfer BCA', 'bank' => 'BCA', 'account_number' => '123-456', 'account_name' => 'Toko', 'is_active' => '1'])->assertSessionHasNoErrors();
        $payment = $this->company->paymentMethods()->where('name', 'Transfer BCA')->firstOrFail();
        $this->assertSame('BCA', $payment->config['bank']);
        $as->put($this->base.'/payments/'.$payment->id, ['type' => 'cod', 'name' => 'COD', 'is_active' => '1'])->assertSessionHasNoErrors();
        $this->assertNull($payment->fresh()->config);

        $as->post($this->base.'/taxes', ['name' => 'PPN', 'rate' => 11, 'is_active' => '1'])->assertSessionHasNoErrors();
        $tax = $this->company->taxes()->firstOrFail();
        $as->put($this->base.'/taxes/'.$tax->id, ['name' => 'PPN 12', 'rate' => 12, 'inclusive' => '1', 'is_active' => '1'])->assertSessionHasNoErrors();
        $this->assertTrue($tax->fresh()->inclusive);

        $product = $this->product();
        $as->put($this->base.'/discounts/'.$product->id, ['sale_price' => 200000])->assertSessionHasNoErrors();
        $this->assertEquals(200000, (float) $product->fresh()->sale_price);

        $as->post($this->base.'/inventory/'.$product->id.'/adjust', ['direction' => 'add', 'quantity' => 5, 'reason' => 'Restock'])->assertSessionHasNoErrors();
        $this->assertSame(15, $product->fresh()->stock);

        foreach (['/categories', '/coupons', '/shipping', '/payments', '/discounts', '/inventory'] as $suffix) {
            $as->get($this->base.$suffix)->assertOk();
        }

        $as->delete($this->base.'/categories/'.$root->id)->assertRedirect();
        $as->delete($this->base.'/coupons/'.$coupon->id)->assertRedirect();
        $as->delete($this->base.'/shipping/'.$shipping->id)->assertRedirect();
        $as->delete($this->base.'/payments/'.$payment->id)->assertRedirect();
        $as->delete($this->base.'/taxes/'.$tax->id)->assertRedirect();
        $this->assertSame(0, $this->company->taxes()->count());
    }

    public function test_order_status_payment_tracking_and_reviews(): void
    {
        $product = $this->product();
        $order = $this->order($product);
        $url = $this->base.'/orders/'.$order->order_number;

        $this->actingAs($this->user)->post($url.'/paid', ['note' => 'Transfer diterima'])->assertSessionHasNoErrors();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('confirmed', $order->fresh()->status);

        $this->actingAs($this->user)->post($url.'/status', ['status' => 'processing'])->assertSessionHasNoErrors();
        $this->actingAs($this->user)->post($url.'/tracking', ['courier' => 'JNE', 'tracking_number' => 'JNE123'])->assertSessionHasNoErrors();
        $this->actingAs($this->user)->post($url.'/status', ['status' => 'shipped', 'note' => 'Dikirim hari ini'])->assertSessionHasNoErrors();
        $this->assertSame('shipped', $order->fresh()->status);
        $this->assertSame(8, $product->fresh()->stock);

        // Invalid transition is rejected.
        $this->actingAs($this->user)->from($url)->post($url.'/status', ['status' => 'pending'])->assertSessionHasErrors('status');

        $this->actingAs($this->user)->get($url)->assertOk()->assertSee('JNE123')->assertSee('Dikirim hari ini')->assertSee('Pesanan selesai');
        $this->actingAs($this->user)->get($this->base.'/orders?filter=shipped')->assertOk()->assertSee($order->order_number);

        $review = $this->company->productReviews()->create(['product_id' => $product->id, 'name' => 'Rina', 'email' => 'rina@example.test', 'rating' => 5, 'body' => 'Mantap', 'status' => 'pending']);
        $this->actingAs($this->user)->post($this->base.'/reviews/'.$review->id.'/status', ['status' => 'approved'])->assertSessionHasNoErrors();
        $this->assertSame('approved', $review->fresh()->status);
        $this->actingAs($this->user)->get($this->base.'/reviews?status=approved')->assertOk()->assertSee('Mantap');
        $this->actingAs($this->user)->delete($this->base.'/reviews/'.$review->id)->assertRedirect();
    }

    public function test_other_users_cannot_access_shop_pages(): void
    {
        $other = $this->userWithPlan();

        $this->actingAs($other)->get($this->base)->assertForbidden();
        $this->actingAs($other)->get($this->base.'/products')->assertForbidden();
    }
}
