<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use Illuminate\Http\Request;
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
            'shops' => CompanyProfile::query()->where('shop_enabled', true)->with(['user', 'shopSetting'])
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

        return view('admin.shop.products', [
            'products' => Product::query()->with(['companyProfile', 'images', 'category'])
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
                ->when(in_array($request->string('status')->toString(), Product::STATUSES, true), fn ($q) => $q->where('status', $request->string('status')->toString()))
                ->latest()->paginate(25)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function orders(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->string('status')->toString();

        return view('admin.shop.orders', [
            'orders' => Order::query()->with('companyProfile')->withCount('items')
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('order_number', 'like', "%{$search}%")->orWhere('customer_email', 'like', "%{$search}%")))
                ->when(array_key_exists($status, Order::STATUSES), fn ($q) => $q->where('status', $status))
                ->latest()->paginate(25)->withQueryString(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function order(Order $order): View
    {
        return view('admin.shop.order', ['order' => $order->load(['items', 'histories.user', 'payment', 'shipment', 'companyProfile'])]);
    }

    public function customers(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        return view('admin.shop.customers', [
            'customers' => Customer::query()->with('companyProfile')->withCount('orders')
                ->withSum(['orders as total_spent' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded'])], 'total')
                ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
                ->latest()->paginate(25)->withQueryString(),
            'search' => $search,
        ]);
    }
}
