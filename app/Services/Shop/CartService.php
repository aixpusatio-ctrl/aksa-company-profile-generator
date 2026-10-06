<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use App\Models\Shop\Coupon;
use App\Models\Shop\Customer;
use App\Models\Shop\Product;
use App\Models\Shop\ProductVariant;
use App\Services\Shop\Shipping\ShippingContext;
use App\Services\Shop\Shipping\ShippingQuote;
use App\Services\Shop\Shipping\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Shopping cart for guests (token kept in the session) and logged-in
 * customers (stored by customer id). Prices are NEVER taken from the
 * browser: every summary is recalculated from the database.
 */
class CartService
{
    public const MAX_QUANTITY = 99;

    public function __construct(
        private readonly CouponService $coupons,
        private readonly TaxService $taxes,
        private readonly ShippingService $shipping,
    ) {}

    // ------------------------------------------------------------ Resolution

    public function customer(CompanyProfile $company): ?Customer
    {
        $customer = Auth::guard('customer')->user();

        return $customer && $customer->company_profile_id === $company->id ? $customer : null;
    }

    public static function token(): string
    {
        if (! session()->has('shop_cart_token')) {
            session(['shop_cart_token' => Str::random(40)]);
        }

        return session('shop_cart_token');
    }

    public function current(CompanyProfile $company, bool $create = false): ?Cart
    {
        $customer = $this->customer($company);

        $query = Cart::query()->where('company_profile_id', $company->id)
            ->when($customer, fn ($q) => $q->where('customer_id', $customer->id), fn ($q) => $q->whereNull('customer_id')->where('session_id', self::token()));

        $cart = $query->latest('id')->first();

        if (! $cart && $create) {
            $cart = Cart::query()->create([
                'company_profile_id' => $company->id,
                'customer_id' => $customer?->id,
                'session_id' => $customer ? null : self::token(),
            ]);
        }

        return $cart;
    }

    // ------------------------------------------------------------ Mutations

    public function add(CompanyProfile $company, int $productId, ?int $variantId, int $quantity): CartItem
    {
        [$product, $variant] = $this->resolvePurchasable($company, $productId, $variantId);
        $cart = $this->current($company, create: true);

        $item = $cart->items()->firstOrNew(['product_id' => $product->id, 'product_variant_id' => $variant?->id]);
        $item->quantity = $this->clampQuantity($product, $variant, ($item->exists ? $item->quantity : 0) + max(1, $quantity));
        $item->save();
        $cart->touch();

        return $item;
    }

    public function update(CompanyProfile $company, int $itemId, int $quantity): void
    {
        $item = $this->findItem($company, $itemId);

        if ($quantity <= 0) {
            $item->delete();

            return;
        }

        $item->quantity = $this->clampQuantity($item->product, $item->variant, $quantity);
        $item->save();
    }

    public function remove(CompanyProfile $company, int $itemId): void
    {
        $this->findItem($company, $itemId)->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update(['coupon_code' => null]);
    }

    public function applyCoupon(CompanyProfile $company, string $code, ?string $email = null): void
    {
        $cart = $this->current($company, create: true);
        $summary = $this->summary($company, $cart);
        $coupon = $this->coupons->validate($company, $code, $summary['subtotal'], $email ?? $this->customer($company)?->email);
        $cart->update(['coupon_code' => $coupon->code]);
    }

    public function removeCoupon(CompanyProfile $company): void
    {
        $this->current($company)?->update(['coupon_code' => null]);
    }

    /**
     * Move the guest cart into the customer's cart after login/registration.
     */
    public function mergeGuestCart(CompanyProfile $company, Customer $customer): void
    {
        $guest = Cart::query()->where('company_profile_id', $company->id)->whereNull('customer_id')
            ->where('session_id', self::token())->with('items.product', 'items.variant')->first();

        if (! $guest || $guest->items->isEmpty()) {
            return;
        }

        $cart = Cart::query()->firstOrCreate(['company_profile_id' => $company->id, 'customer_id' => $customer->id]);

        foreach ($guest->items as $item) {
            if (! $item->product) {
                continue;
            }
            $line = $cart->items()->firstOrNew(['product_id' => $item->product_id, 'product_variant_id' => $item->product_variant_id]);
            $line->quantity = $this->clampQuantity($item->product, $item->variant, ($line->exists ? $line->quantity : 0) + $item->quantity);
            $line->save();
        }

        $cart->update(['coupon_code' => $cart->coupon_code ?: $guest->coupon_code]);
        $guest->delete();
    }

    // ------------------------------------------------------------ Summary

    /**
     * Full price breakdown, recalculated from the database.
     *
     * @return array{lines: array, count: int, subtotal: float, discount: float, coupon: ?Coupon,
     *               coupon_error: ?string, shipping: float, shipping_quote: ?ShippingQuote,
     *               tax: float, tax_name: ?string, tax_inclusive: bool, total: float, weight: int, errors: array}
     */
    public function summary(CompanyProfile $company, ?Cart $cart, array $options = []): array
    {
        $cart?->loadMissing(['items.product.images', 'items.product.variants', 'items.variant']);
        $lines = [];
        $errors = [];

        foreach ($cart?->items ?? [] as $item) {
            $product = $item->product;
            $variant = $item->variant;

            if (! $product || ! $product->isPublished() || $product->company_profile_id !== $company->id || ($product->hasVariants() && ! $variant)) {
                $errors[] = 'Sebuah produk di keranjang sudah tidak tersedia dan dihapus.';
                $item->delete();

                continue;
            }

            $unit = $variant ? $variant->currentPrice($product) : $product->currentPrice();
            $original = $variant ? $variant->basePrice($product) : ($product->originalPrice() ?? (float) $product->price);
            $available = $product->track_stock && $product->stock_status !== 'backorder' ? ($variant ? $variant->availableStock() : $product->availableStock()) : null;
            $lineError = $available !== null && $item->quantity > $available
                ? ($available > 0 ? "Stok tersisa {$available}." : 'Stok habis.')
                : null;

            if ($lineError) {
                $errors[] = $product->name.': '.$lineError;
            }

            $lines[] = [
                'id' => $item->id,
                'product' => $product,
                'variant' => $variant,
                'name' => $product->name,
                'variant_label' => $variant?->label,
                'sku' => $variant?->sku ?? $product->sku,
                'image' => $variant?->url('image') ?? $product->mainImage(),
                'unit_price' => $unit,
                'original_price' => $original > $unit ? $original : null,
                'quantity' => $item->quantity,
                'line_total' => $unit * $item->quantity,
                'weight' => (int) ($variant?->weight ?? $product->weight ?? 0) * $item->quantity,
                'available' => $available,
                'error' => $lineError,
            ];
        }

        $subtotal = (float) array_sum(array_column($lines, 'line_total'));
        $count = (int) array_sum(array_column($lines, 'quantity'));
        $weight = (int) array_sum(array_column($lines, 'weight'));

        // Coupon
        $coupon = null;
        $couponError = null;
        $code = $options['coupon'] ?? $cart?->coupon_code;
        if ($code && $lines) {
            try {
                $coupon = $this->coupons->validate($company, $code, $subtotal, $options['email'] ?? $this->customer($company)?->email);
            } catch (ValidationException $e) {
                $couponError = collect($e->errors())->flatten()->first();
            }
        }
        $discount = $coupon ? $this->coupons->discount($coupon, $subtotal) : 0.0;

        // Shipping
        $quote = null;
        if (! empty($options['shipping_method_id']) && $lines) {
            $quote = $this->shipping->quote($company, (int) $options['shipping_method_id'], new ShippingContext(
                subtotal: $subtotal - $discount,
                weightGrams: $weight,
                quantity: $count,
                city: $options['city'] ?? null,
                province: $options['province'] ?? null,
                postalCode: $options['postal_code'] ?? null,
                freeShippingCoupon: $coupon?->type === 'free_shipping',
            ));
        }
        $shippingCost = $quote?->cost ?? 0.0;

        // Tax (applied to goods after discount)
        $tax = $this->taxes->calculate($this->taxes->active($company), max(0, $subtotal - $discount));
        $total = max(0, $subtotal - $discount) + $shippingCost + ($tax['inclusive'] ? 0 : $tax['amount']);

        return [
            'lines' => $lines,
            'count' => $count,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon' => $coupon,
            'coupon_error' => $couponError,
            'shipping' => $shippingCost,
            'shipping_quote' => $quote,
            'tax' => $tax['amount'],
            'tax_name' => $tax['name'],
            'tax_inclusive' => $tax['inclusive'],
            'total' => round($total, 2),
            'weight' => $weight,
            'errors' => $errors,
        ];
    }

    public function count(CompanyProfile $company): int
    {
        return (int) ($this->current($company)?->items()->sum('quantity') ?? 0);
    }

    // ------------------------------------------------------------ Helpers

    /**
     * @return array{0: Product, 1: ?ProductVariant}
     */
    public function resolvePurchasable(CompanyProfile $company, int $productId, ?int $variantId): array
    {
        $product = Product::query()->visible()->where('company_profile_id', $company->id)->with('variants')->find($productId);

        if (! $product) {
            throw ValidationException::withMessages(['product' => 'Produk tidak tersedia.']);
        }

        $variant = null;
        if ($product->hasVariants()) {
            $variant = $product->variants->where('is_active', true)->firstWhere('id', $variantId);
            if (! $variant) {
                throw ValidationException::withMessages(['variant' => 'Pilih varian produk terlebih dahulu.']);
            }
        }

        $available = $variant ? $variant->availableStock() : $product->availableStock();
        if ($product->stock_status === 'out_of_stock' || ($product->track_stock && $product->stock_status !== 'backorder' && $available <= 0)) {
            throw ValidationException::withMessages(['product' => 'Stok produk habis.']);
        }

        return [$product, $variant];
    }

    private function clampQuantity(Product $product, ?ProductVariant $variant, int $quantity): int
    {
        $max = self::MAX_QUANTITY;

        if ($product->track_stock && $product->stock_status !== 'backorder') {
            $max = min($max, $variant ? $variant->availableStock() : $product->availableStock());
        }

        return max(1, min($quantity, max(1, $max)));
    }

    private function findItem(CompanyProfile $company, int $itemId): CartItem
    {
        $cart = $this->current($company);
        $item = $cart?->items()->with('product', 'variant')->find($itemId);

        if (! $item) {
            throw ValidationException::withMessages(['cart' => 'Item keranjang tidak ditemukan.']);
        }

        return $item;
    }
}
