<?php

namespace Tests\Feature\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Coupon;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\User;
use App\Services\Shop\OrderService;
use App\Services\Shop\ProductService;
use App\Services\Shop\ShopService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = $this->userWithPlan();
        $this->company = $this->companyFor($this->owner, ['slug' => 'tokoku', 'whatsapp' => '081234567890'], published: true);
        app(ShopService::class)->enable($this->company);
    }

    private function product(array $data = []): Product
    {
        return app(ProductService::class)->save($this->company, $data + [
            'name' => 'Kursi Rotan', 'price' => 100000, 'stock' => 5, 'track_stock' => true, 'status' => 'published',
        ], $this->owner);
    }

    private function url(string $path): string
    {
        return $this->tenantUrl($this->company, $path);
    }

    private function checkoutData(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Budi', 'email' => 'budi@example.com', 'phone' => '08123456789',
            'address' => ['address' => 'Jl. Mawar 1', 'city' => 'Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40111'],
            'shipping_method_id' => $this->company->shippingMethods()->where('type', 'pickup')->value('id'),
            'payment_method_id' => $this->company->paymentMethods()->where('type', 'bank_transfer')->value('id'),
            'terms' => '1',
        ];
    }

    public function test_shop_routes_404_when_shop_disabled(): void
    {
        app(ShopService::class)->disable($this->company->fresh());

        $this->postJson($this->url('/shop/cart'), ['product_id' => 1])->assertNotFound();
    }

    public function test_guest_can_add_to_cart_and_totals_come_from_database(): void
    {
        $product = $this->product();

        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id, 'quantity' => 2, 'price' => 1])
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('items.0.name', 'Kursi Rotan');

        $this->post($this->url('/shop/checkout'), $this->checkoutData(['total' => 1, 'subtotal' => 1]))->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertSame(200000.0, (float) $order->subtotal);
        $this->assertSame(200000.0, (float) $order->total);
        $this->assertSame(2, $product->fresh()->reserved_stock);
        $this->assertSame('pending', $order->status);
    }

    public function test_cannot_add_more_than_available_stock_or_unpublished_products(): void
    {
        $product = $this->product(['stock' => 2]);
        $draft = $this->product(['name' => 'Draft', 'status' => 'draft']);

        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id, 'quantity' => 10])->assertOk()->assertJsonPath('count', 2);
        $this->postJson($this->url('/shop/cart'), ['product_id' => $draft->id])->assertStatus(422);
    }

    public function test_stock_cannot_be_oversold_at_checkout(): void
    {
        $product = $this->product(['stock' => 1]);

        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id])->assertOk();
        // Someone else buys the last item in the meantime.
        $product->forceFill(['reserved_stock' => 1])->save();

        $this->post($this->url('/shop/checkout'), $this->checkoutData())->assertSessionHasErrors();
        $this->assertSame(0, Order::query()->count());
    }

    public function test_product_from_another_shop_cannot_be_added(): void
    {
        $other = $this->companyFor($this->owner, ['slug' => 'lain'], published: true);
        app(ShopService::class)->enable($other);
        $foreign = app(ProductService::class)->save($other, ['name' => 'Asing', 'price' => 5000, 'stock' => 3, 'status' => 'published'], $this->owner);

        $this->postJson($this->url('/shop/cart'), ['product_id' => $foreign->id])->assertStatus(422);
    }

    public function test_coupon_applies_discount_and_respects_usage_limit(): void
    {
        $product = $this->product();
        Coupon::query()->create(['company_profile_id' => $this->company->id, 'code' => 'HEMAT10', 'type' => 'percentage', 'value' => 10, 'usage_limit' => 1, 'status' => 'active']);

        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id])->assertOk();
        $this->postJson($this->url('/shop/cart/coupon'), ['code' => 'hemat10'])->assertOk()->assertJsonPath('coupon', 'HEMAT10');
        $this->post($this->url('/shop/checkout'), $this->checkoutData())->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertSame(10000.0, (float) $order->discount);
        $this->assertSame(90000.0, (float) $order->total);

        // Limit reached: a second customer cannot use it.
        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id])->assertOk();
        $this->postJson($this->url('/shop/cart/coupon'), ['code' => 'HEMAT10'])->assertStatus(422);
    }

    public function test_order_page_requires_token(): void
    {
        $product = $this->product();
        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id])->assertOk();
        $this->post($this->url('/shop/checkout'), $this->checkoutData())->assertRedirect();
        $order = Order::query()->firstOrFail();

        $this->get($this->url('/shop/order/'.$order->order_number))->assertNotFound();
        $this->get($this->url('/shop/order/'.$order->order_number.'?token=wrong'))->assertNotFound();
    }

    public function test_whatsapp_checkout_creates_order_and_links_to_prefilled_message(): void
    {
        $product = $this->product();
        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id])->assertOk();

        $response = $this->post($this->url('/shop/checkout/whatsapp'), ['name' => 'Sari', 'email' => 'sari@example.com', 'phone' => '0811', 'terms' => '1']);

        $order = Order::query()->firstOrFail();
        $response->assertRedirect($this->url('/shop/order/'.$order->order_number.'?token='.$order->access_token.'&wa=1'));
        $this->assertSame('whatsapp', $order->channel);
        $this->assertStringContainsString($order->order_number, urldecode((string) app(\App\Services\Shop\CheckoutService::class)->whatsappUrl($this->company, $order)));
    }

    public function test_cancelling_an_order_releases_reserved_stock(): void
    {
        $product = $this->product(['stock' => 3]);
        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id, 'quantity' => 2])->assertOk();
        $this->post($this->url('/shop/checkout'), $this->checkoutData())->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertSame(2, $product->fresh()->reserved_stock);

        app(OrderService::class)->updateStatus($order, 'cancelled');
        $this->assertSame(0, $product->fresh()->reserved_stock);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_completing_an_order_commits_stock(): void
    {
        $product = $this->product(['stock' => 3]);
        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id])->assertOk();
        $this->post($this->url('/shop/checkout'), $this->checkoutData())->assertRedirect();
        $order = Order::query()->firstOrFail();

        $orders = app(OrderService::class);
        foreach (['confirmed', 'processing', 'packed', 'shipped', 'completed'] as $status) {
            $order = $orders->updateStatus($order, $status);
        }

        $product->refresh();
        $this->assertSame(2, $product->stock);
        $this->assertSame(0, $product->reserved_stock);
    }

    public function test_variant_prices_are_used(): void
    {
        $product = $this->product([
            'stock' => 0,
            'options' => [['name' => 'Ukuran', 'values' => 'S, L']],
            'variants' => ['S' => ['price' => 90000, 'stock' => 4], 'L' => ['price' => 120000, 'stock' => 4]],
        ]);
        $large = $product->variants->firstWhere('label', 'L');

        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id])->assertStatus(422); // variant required
        $this->postJson($this->url('/shop/cart'), ['product_id' => $product->id, 'variant_id' => $large->id])->assertOk();
        $this->post($this->url('/shop/checkout'), $this->checkoutData())->assertRedirect();

        $this->assertSame(120000.0, (float) Order::query()->firstOrFail()->subtotal);
        $this->assertSame(1, $large->fresh()->reserved_stock);
    }
}
