<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Platform-wide e-commerce overview for admins (all shops, products,
 * orders, customers). Sellers only ever see their own company's data.
 */
class ShopController extends Controller
{
    public function shops(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        return view('admin.shop.shops', [
            'shops' => $this->shopsQuery()->with(['user', 'shopSetting'])
                ->withCount(['shopProducts', 'orders', 'customers'])
                ->withSum(['orders as revenue' => fn ($q) => $q->where('payment_status', 'paid')->whereNotIn('status', ['cancelled', 'refunded'])], 'total')
                ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->orderByDesc('orders_count')->paginate(20)->withQueryString(),
            'totals' => [
                'shops' => CompanyProfile::query()->where('shop_enabled', true)->count(),
                'products' => Product::query()->count(),
                'orders' => Order::query()->count(),
                'gmv' => (float) Order::query()->where('payment_status', 'paid')->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
                'customers' => Customer::query()->count(),
            ],
            'search' => $search,
        ]);
    }

    public function products(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();
        $shop = $request->integer('shop');

        return view('admin.shop.products', [
            'products' => Product::query()->with(['companyProfile.shopSetting', 'images', 'category'])
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
                ->when(in_array($status, Product::STATUSES, true), fn ($q) => $q->where('status', $status))
                ->when($shop, fn ($q) => $q->where('company_profile_id', $shop))
                ->latest()->paginate(25)->withQueryString(),
            'search' => $search,
            'status' => $status,
            'shop' => $shop,
            'shopOptions' => $this->shopOptions(),
        ]);
    }

    public function orders(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();
        $payment = $request->string('payment')->toString();
        $shop = $request->integer('shop');

        return view('admin.shop.orders', [
            'orders' => Order::query()->with('companyProfile')->withCount('items')
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")->orWhere('customer_name', 'like', "%{$search}%")))
                ->when(array_key_exists($status, Order::STATUSES), fn ($q) => $q->where('status', $status))
                ->when(array_key_exists($payment, Order::PAYMENT_STATUSES), fn ($q) => $q->where('payment_status', $payment))
                ->when($shop, fn ($q) => $q->where('company_profile_id', $shop))
                ->latest()->paginate(25)->withQueryString(),
            'search' => $search,
            'status' => $status,
            'payment' => $payment,
            'shop' => $shop,
            'shopOptions' => $this->shopOptions(),
        ]);
    }

    public function order(Order $order): View
    {
        return view('admin.shop.order', ['order' => $order->load(['items', 'histories.user', 'payment', 'shipment', 'companyProfile'])]);
    }

    public function customers(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $shop = $request->integer('shop');

        return view('admin.shop.customers', [
            'customers' => Customer::query()->with('companyProfile.shopSetting')->withCount('orders')
                ->withSum(['orders as total_spent' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded'])], 'total')
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
                ->when($shop, fn ($q) => $q->where('company_profile_id', $shop))
                ->latest()->paginate(25)->withQueryString(),
            'search' => $search,
            'shop' => $shop,
            'shopOptions' => $this->shopOptions(),
        ]);
    }

    /** Companies with the shop module enabled, or that still hold shop data after turning it off. */
    private function shopsQuery(): Builder
    {
        return CompanyProfile::query()->where(fn ($q) => $q->where('shop_enabled', true)->orWhereHas('shopProducts')->orWhereHas('orders'));
    }

    /** @return Collection<int, string> id => name, for the shop filter dropdowns. */
    private function shopOptions(): Collection
    {
        return $this->shopsQuery()->orderBy('name')->pluck('name', 'id');
    }
}
