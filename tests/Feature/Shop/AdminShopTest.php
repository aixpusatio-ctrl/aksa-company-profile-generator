<?php

namespace Tests\Feature\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminShopTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private CompanyProfile $shop;

    private CompanyProfile $otherShop;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->admin();

        $this->shop = $this->makeShop('PT Toko Alpha');
        $this->otherShop = $this->makeShop('PT Toko Beta');

        Product::query()->create(['company_profile_id' => $this->shop->id, 'name' => 'Kopi Arabika', 'slug' => 'kopi-arabika', 'sku' => 'KOPI-01', 'price' => 85000, 'stock' => 10, 'status' => 'published']);
        Product::query()->create(['company_profile_id' => $this->otherShop->id, 'name' => 'Teh Hijau', 'slug' => 'teh-hijau', 'price' => 30000, 'status' => 'draft']);

        $customer = Customer::query()->create(['company_profile_id' => $this->shop->id, 'name' => 'Budi Pembeli', 'email' => 'budi@example.test']);
        Customer::query()->create(['company_profile_id' => $this->otherShop->id, 'name' => 'Sari Lain', 'email' => 'sari@example.test']);

        $this->order = $this->makeOrder($this->shop, 'INV-0001', ['customer_id' => $customer->id, 'customer_name' => 'Budi Pembeli', 'customer_email' => 'budi@example.test', 'payment_status' => 'paid', 'status' => 'processing']);
        $this->makeOrder($this->otherShop, 'INV-0999', ['customer_name' => 'Sari Lain', 'customer_email' => 'sari@example.test']);

        $this->order->items()->create(['product_name' => 'Kopi Arabika (snapshot)', 'variant_label' => '250g', 'sku' => 'KOPI-01', 'price' => 85000, 'quantity' => 2, 'subtotal' => 170000]);
        $this->order->histories()->create(['type' => 'order', 'status' => 'pending', 'note' => 'Pesanan dibuat']);
        $this->order->histories()->create(['type' => 'payment', 'status' => 'paid', 'user_id' => $this->admin->id]);
        $this->order->payment()->create(['provider' => 'manual', 'method' => 'bank_transfer', 'amount' => 170000, 'status' => 'paid', 'reference' => 'TRF-123']);
        $this->order->shipment()->create(['provider' => 'manual', 'method' => 'JNE REG', 'courier' => 'JNE', 'tracking_number' => 'RESI-777']);
    }

    private function makeShop(string $name): CompanyProfile
    {
        $company = $this->companyFor(null, ['name' => $name], published: true);
        $company->forceFill(['shop_enabled' => true])->save();

        return $company;
    }

    private function makeOrder(CompanyProfile $company, string $number, array $attributes = []): Order
    {
        return Order::query()->create(array_merge([
            'company_profile_id' => $company->id,
            'order_number' => $number,
            'access_token' => Str::random(40),
            'subtotal' => 170000,
            'shipping_cost' => 15000,
            'total' => 185000,
            'customer_name' => 'Pembeli',
            'customer_email' => 'pembeli@example.test',
            'shipping_address' => ['name' => 'Budi', 'address' => 'Jl. Merdeka 1', 'city' => 'Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40111'],
        ], $attributes));
    }

    public function test_admin_can_view_shops(): void
    {
        $this->actingAs($this->admin)->get(route('admin.shop.shops'))
            ->assertOk()->assertSee('PT Toko Alpha')->assertSee('PT Toko Beta')->assertSee('Rp 185.000');

        $this->actingAs($this->admin)->get(route('admin.shop.shops', ['q' => 'Alpha']))
            ->assertOk()->assertSee('PT Toko Alpha')->assertDontSee('PT Toko Beta');
    }

    public function test_admin_can_view_and_filter_products(): void
    {
        $this->actingAs($this->admin)->get(route('admin.shop.products'))
            ->assertOk()->assertSee('Kopi Arabika')->assertSee('Teh Hijau');

        $this->actingAs($this->admin)->get(route('admin.shop.products', ['shop' => $this->shop->id]))
            ->assertOk()->assertSee('Kopi Arabika')->assertDontSee('Teh Hijau');

        $this->actingAs($this->admin)->get(route('admin.shop.products', ['status' => 'draft']))
            ->assertOk()->assertSee('Teh Hijau')->assertDontSee('Kopi Arabika');

        $this->actingAs($this->admin)->get(route('admin.shop.products', ['q' => 'KOPI-01']))
            ->assertOk()->assertSee('Kopi Arabika')->assertDontSee('Teh Hijau');
    }

    public function test_admin_can_view_and_filter_orders(): void
    {
        $this->actingAs($this->admin)->get(route('admin.shop.orders'))
            ->assertOk()->assertSee('INV-0001')->assertSee('INV-0999');

        $this->actingAs($this->admin)->get(route('admin.shop.orders', ['shop' => $this->otherShop->id]))
            ->assertOk()->assertSee('INV-0999')->assertDontSee('INV-0001');

        $this->actingAs($this->admin)->get(route('admin.shop.orders', ['payment' => 'paid']))
            ->assertOk()->assertSee('INV-0001')->assertDontSee('INV-0999');

        $this->actingAs($this->admin)->get(route('admin.shop.orders', ['status' => 'processing']))
            ->assertOk()->assertSee('INV-0001')->assertDontSee('INV-0999');

        $this->actingAs($this->admin)->get(route('admin.shop.orders', ['q' => 'sari@']))
            ->assertOk()->assertSee('INV-0999')->assertDontSee('INV-0001');
    }

    public function test_admin_can_view_order_detail(): void
    {
        $this->actingAs($this->admin)->get(route('admin.shop.orders.show', $this->order->id))
            ->assertOk()
            ->assertSee('INV-0001')
            ->assertSee('Kopi Arabika (snapshot)')
            ->assertSee('250g')
            ->assertSee('budi@example.test')
            ->assertSee('Jl. Merdeka 1')
            ->assertSee('TRF-123')
            ->assertSee('RESI-777')
            ->assertSee('Pesanan dibuat')
            ->assertSee('Rp 185.000');
    }

    public function test_admin_can_view_and_filter_customers(): void
    {
        $this->actingAs($this->admin)->get(route('admin.shop.customers'))
            ->assertOk()->assertSee('Budi Pembeli')->assertSee('Sari Lain');

        $this->actingAs($this->admin)->get(route('admin.shop.customers', ['shop' => $this->shop->id]))
            ->assertOk()->assertSee('Budi Pembeli')->assertDontSee('Sari Lain');

        $this->actingAs($this->admin)->get(route('admin.shop.customers', ['q' => 'sari']))
            ->assertOk()->assertSee('Sari Lain')->assertDontSee('Budi Pembeli');
    }

    public function test_admin_pages_render_when_empty(): void
    {
        Order::query()->delete();
        Product::query()->delete();
        Customer::query()->delete();
        CompanyProfile::query()->update(['shop_enabled' => false]);

        foreach (['admin.shop.shops', 'admin.shop.products', 'admin.shop.orders', 'admin.shop.customers'] as $route) {
            $this->actingAs($this->admin)->get(route($route))->assertOk();
        }
    }

    public function test_admin_dashboard_and_nav_show_ecommerce(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()->assertSee('E-Commerce')->assertSee(route('admin.shop.orders'));
    }

    public function test_regular_users_cannot_access_admin_shop_pages(): void
    {
        $user = $this->shop->user;

        foreach ([
            route('admin.shop.shops'),
            route('admin.shop.products'),
            route('admin.shop.orders'),
            route('admin.shop.orders.show', $this->order->id),
            route('admin.shop.customers'),
        ] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.shop.orders'))->assertRedirect(route('login'));
    }
}
