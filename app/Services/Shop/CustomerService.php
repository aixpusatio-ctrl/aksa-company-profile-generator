<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\CustomerAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Customer accounts of a shop (separate "customer" guard), addresses and
 * the seller's customer list.
 */
class CustomerService
{
    public function __construct(
        private readonly CartService $carts,
        private readonly WishlistService $wishlists,
    ) {}

    public function register(CompanyProfile $company, array $data): Customer
    {
        $email = Str::lower(trim($data['email']));

        // A guest who already ordered becomes a registered customer.
        $customer = Customer::query()->firstOrNew(['company_profile_id' => $company->id, 'email' => $email]);
        $customer->fill([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? $customer->phone,
            'whatsapp' => $data['whatsapp'] ?? $customer->whatsapp,
            'password' => $data['password'],
        ])->save();

        $this->login($company, $customer);

        return $customer;
    }

    /**
     * Log a customer in and merge their guest cart & wishlist.
     */
    public function login(CompanyProfile $company, Customer $customer, bool $remember = false): void
    {
        $this->carts->mergeGuestCart($company, $customer);
        $this->wishlists->merge($company, $customer);

        Auth::guard('customer')->login($customer, $remember);
        session()->regenerate();
        $customer->forceFill(['last_login_at' => now()])->save();
    }

    public function attempt(CompanyProfile $company, string $email, string $password, bool $remember = false): bool
    {
        $customer = Customer::query()->where('company_profile_id', $company->id)->where('email', Str::lower(trim($email)))->first();

        if (! $customer || ! $customer->password || ! Auth::guard('customer')->getProvider()->validateCredentials($customer, ['password' => $password])) {
            return false;
        }

        $this->login($company, $customer, $remember);

        return true;
    }

    public function saveAddress(Customer $customer, array $data, ?CustomerAddress $address = null): CustomerAddress
    {
        return DB::transaction(function () use ($customer, $data, $address) {
            $makeDefault = ! empty($data['is_default']) || ! $customer->addresses()->exists();
            if ($makeDefault) {
                $customer->addresses()->update(['is_default' => false]);
            }

            $data['is_default'] = $makeDefault || ($address?->is_default ?? false);

            return $address ? tap($address)->update($data) : $customer->addresses()->create($data);
        });
    }

    /**
     * Seller customer list with order aggregates.
     */
    public function listQuery(CompanyProfile $company, ?string $search = null): Builder
    {
        return Customer::query()
            ->where('company_profile_id', $company->id)
            ->withCount(['orders' => fn ($q) => $q->whereNotIn('status', ['cancelled'])])
            ->withSum(['orders as total_spent' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded'])], 'total')
            ->withMax('orders as last_order_at', 'created_at')
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")));
    }
}
