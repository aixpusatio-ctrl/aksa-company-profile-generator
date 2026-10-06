<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\Wishlist;
use App\Services\Shop\CustomerService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends SellerController
{
    public function index(Request $request, CompanyProfile $company, CustomerService $customers): View
    {
        $this->authorizeShop($company);
        $search = $request->string('q')->trim()->toString();

        return view('dashboard.shop.customers.index', [
            'company' => $company,
            'customers' => $customers->listQuery($company, $search)->latest()->paginate(20)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function show(CompanyProfile $company, Customer $customer): View
    {
        $this->authorizeShop($company);

        $wishlist = Wishlist::query()->where('customer_id', $customer->id)->with('items.product')->first();

        return view('dashboard.shop.customers.show', [
            'company' => $company,
            'customer' => $customer->load('addresses'),
            'orders' => $customer->orders()->withCount('items')->get(),
            'totalSpent' => (float) $customer->orders()->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
            'wishlist' => $wishlist?->items->pluck('product')->filter() ?? collect(),
            'reviews' => $customer->reviews()->with('product')->get(),
        ]);
    }
}
