<?php

namespace App\Http\Controllers\Shop;

use App\Models\Shop\Customer;
use App\Services\Shop\CatalogService;
use App\Services\Shop\CustomerService;
use App\Services\Shop\OrderService;
use App\Services\Shop\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Customer account area ("My Account") on the shop's website. Uses the
 * separate "customer" guard — customers cannot access seller/admin areas.
 */
class AccountController extends ShopController
{
    public function __construct(private readonly CustomerService $customers) {}

    public function loginForm(Request $request)
    {
        return $this->shopView($request, 'websites.shop.account.login', [], ['title' => 'Masuk', 'robots' => 'noindex,nofollow']);
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);

        if (! $this->customers->attempt($this->company(), $data['email'], $data['password'], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        return redirect()->intended($this->site($request)->account());
    }

    public function registerForm(Request $request)
    {
        return $this->shopView($request, 'websites.shop.account.register', [], ['title' => 'Daftar', 'robots' => 'noindex,nofollow']);
    }

    public function register(Request $request)
    {
        $company = $this->company();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('customers')->where('company_profile_id', $company->id)->whereNotNull('password')],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], ['email.unique' => 'Email sudah terdaftar. Silakan masuk.']);

        $this->customers->register($company, $data);

        return redirect()->intended($this->site($request)->account())->with('shop_toast', 'Akun berhasil dibuat. Selamat berbelanja!');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->regenerateToken();

        return redirect()->to($this->site($request)->shop());
    }

    public function profile(Request $request)
    {
        $customer = $this->customer();

        return $this->shopView($request, 'websites.shop.account.profile', [
            'recentOrders' => $customer->orders()->take(3)->get(),
            'stats' => [
                'orders' => $customer->orders()->count(),
                'spent' => (float) $customer->orders()->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
                'wishlist' => count(app(WishlistService::class)->ids($this->company())),
            ],
        ], ['title' => 'Akun Saya', 'robots' => 'noindex,nofollow']);
    }

    public function updateProfile(Request $request)
    {
        $customer = $this->customer();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:customer'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $customer->fill(collect($data)->only(['name', 'phone', 'whatsapp'])->all());
        if (! empty($data['password'])) {
            $customer->password = $data['password'];
        }
        $customer->save();

        return back()->with('shop_toast', 'Profil diperbarui.');
    }

    public function orders(Request $request)
    {
        return $this->shopView($request, 'websites.shop.account.orders', [
            'orders' => $this->customer()->orders()->withCount('items')->paginate(10),
        ], ['title' => 'Pesanan Saya', 'robots' => 'noindex,nofollow']);
    }

    public function order(Request $request, string $number, OrderService $orders)
    {
        $order = $this->customer()->orders()->where('order_number', $number)->with(['items', 'histories', 'payment', 'shipment'])->firstOrFail();

        return $this->shopView($request, 'websites.shop.order', [
            'order' => $order,
            'timeline' => $orders->timeline($order),
            'whatsappUrl' => null,
            'justPlaced' => false,
            'inAccount' => true,
        ], ['title' => 'Pesanan #'.$order->order_number, 'robots' => 'noindex,nofollow']);
    }

    public function wishlist(Request $request, CatalogService $catalog)
    {
        $ids = app(WishlistService::class)->ids($this->company());

        return $this->shopView($request, 'websites.shop.wishlist', [
            'products' => $ids ? $catalog->baseQuery($this->company())->whereIn('id', $ids)->get() : collect(),
            'inAccount' => true,
        ], ['title' => 'Wishlist', 'robots' => 'noindex,nofollow']);
    }

    public function reviews(Request $request)
    {
        return $this->shopView($request, 'websites.shop.account.reviews', [
            'reviews' => $this->customer()->reviews()->with('product')->paginate(10),
        ], ['title' => 'Ulasan Saya', 'robots' => 'noindex,nofollow']);
    }

    public function addresses(Request $request)
    {
        return $this->shopView($request, 'websites.shop.account.addresses', [
            'addresses' => $this->customer()->addresses,
        ], ['title' => 'Alamat', 'robots' => 'noindex,nofollow']);
    }

    public function storeAddress(Request $request)
    {
        $this->customers->saveAddress($this->customer(), $this->validatedAddress($request));

        return back()->with('shop_toast', 'Alamat disimpan.');
    }

    public function updateAddress(Request $request, int $address)
    {
        $model = $this->customer()->addresses()->findOrFail($address);
        $this->customers->saveAddress($this->customer(), $this->validatedAddress($request), $model);

        return back()->with('shop_toast', 'Alamat diperbarui.');
    }

    public function destroyAddress(int $address)
    {
        $this->customer()->addresses()->findOrFail($address)->delete();

        return back()->with('shop_toast', 'Alamat dihapus.');
    }

    private function validatedAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function customer(): Customer
    {
        $customer = Auth::guard('customer')->user();
        abort_unless($customer && $customer->company_profile_id === $this->company()->id, 403);

        return $customer;
    }
}
