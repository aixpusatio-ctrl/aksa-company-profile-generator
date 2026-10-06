<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\Product;
use App\Models\Shop\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Wishlist for guests (session token) and customers (database).
 */
class WishlistService
{
    public function current(CompanyProfile $company, bool $create = false): ?Wishlist
    {
        $customer = Auth::guard('customer')->user();
        $customer = $customer && $customer->company_profile_id === $company->id ? $customer : null;

        $query = Wishlist::query()->where('company_profile_id', $company->id)
            ->when($customer, fn ($q) => $q->where('customer_id', $customer->id), fn ($q) => $q->whereNull('customer_id')->where('session_id', CartService::token()));

        $wishlist = $query->first();

        if (! $wishlist && $create) {
            $wishlist = Wishlist::query()->create([
                'company_profile_id' => $company->id,
                'customer_id' => $customer?->id,
                'session_id' => $customer ? null : CartService::token(),
            ]);
        }

        return $wishlist;
    }

    /** @return array<int, int> product ids */
    public function ids(CompanyProfile $company): array
    {
        return $this->current($company)?->items()->pluck('product_id')->all() ?? [];
    }

    /** Toggle a product; returns true when it is now in the wishlist. */
    public function toggle(CompanyProfile $company, int $productId): bool
    {
        $product = Product::query()->visible()->where('company_profile_id', $company->id)->find($productId)
            ?? throw ValidationException::withMessages(['product' => 'Produk tidak tersedia.']);

        $wishlist = $this->current($company, create: true);
        $existing = $wishlist->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        $wishlist->items()->create(['product_id' => $product->id]);

        return true;
    }

    public function remove(CompanyProfile $company, int $productId): void
    {
        $this->current($company)?->items()->where('product_id', $productId)->delete();
    }

    public function merge(CompanyProfile $company, Customer $customer): void
    {
        $guest = Wishlist::query()->where('company_profile_id', $company->id)->whereNull('customer_id')
            ->where('session_id', CartService::token())->with('items')->first();

        if (! $guest) {
            return;
        }

        $wishlist = Wishlist::query()->firstOrCreate(['company_profile_id' => $company->id, 'customer_id' => $customer->id]);
        foreach ($guest->items as $item) {
            $wishlist->items()->firstOrCreate(['product_id' => $item->product_id]);
        }
        $guest->delete();
    }
}
