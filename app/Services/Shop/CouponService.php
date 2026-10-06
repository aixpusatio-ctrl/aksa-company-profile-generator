<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Coupon;
use App\Models\Shop\CouponUsage;
use App\Models\Shop\Order;
use App\Support\Shop\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Coupon validation & redemption. Limits are enforced again inside the
 * order transaction (atomic used_count increment) to prevent abuse with
 * parallel checkouts.
 */
class CouponService
{
    public function find(CompanyProfile $company, ?string $code): ?Coupon
    {
        $code = Str::upper(trim((string) $code));

        return $code === '' ? null : $company->coupons()->where('code', $code)->first();
    }

    /**
     * @throws ValidationException
     */
    public function validate(CompanyProfile $company, ?string $code, float $subtotal, ?string $email = null): Coupon
    {
        $coupon = $this->find($company, $code);
        $fail = fn (string $message) => throw ValidationException::withMessages(['coupon' => $message]);

        if (! $coupon || $coupon->status !== 'active') {
            $fail('Kode kupon tidak valid.');
        }
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $fail('Kupon belum berlaku.');
        }
        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            $fail('Kupon sudah berakhir.');
        }
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            $fail('Kuota kupon sudah habis.');
        }
        if ($coupon->min_purchase !== null && $subtotal < (float) $coupon->min_purchase) {
            $fail('Minimal belanja '.Money::format($coupon->min_purchase).' untuk kupon ini.');
        }
        if ($email && $coupon->usage_limit_per_customer !== null
            && $coupon->usages()->where('email', Str::lower($email))->count() >= $coupon->usage_limit_per_customer) {
            $fail('Anda sudah menggunakan kupon ini.');
        }

        return $coupon;
    }

    /** Discount on the subtotal (free-shipping coupons discount shipping instead). */
    public function discount(Coupon $coupon, float $subtotal): float
    {
        $discount = match ($coupon->type) {
            'percentage' => $subtotal * min(100, (float) $coupon->value) / 100,
            'fixed' => (float) $coupon->value,
            default => 0.0,
        };

        if ($coupon->max_discount !== null) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        return max(0.0, min($discount, $subtotal));
    }

    /**
     * Record usage atomically; fails if the global limit was reached meanwhile.
     */
    public function redeem(Coupon $coupon, Order $order, float $discount): void
    {
        $updated = Coupon::query()->whereKey($coupon->id)
            ->where(fn ($q) => $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit'))
            ->update(['used_count' => DB::raw('used_count + 1')]);

        if (! $updated) {
            throw ValidationException::withMessages(['coupon' => 'Kuota kupon sudah habis.']);
        }

        CouponUsage::query()->create([
            'coupon_id' => $coupon->id,
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'email' => Str::lower($order->customer_email),
            'discount' => $discount,
        ]);
    }

    /** Give the usage back when an order is cancelled. */
    public function revoke(Order $order): void
    {
        $usages = CouponUsage::query()->where('order_id', $order->id)->get();

        foreach ($usages as $usage) {
            Coupon::query()->whereKey($usage->coupon_id)->where('used_count', '>', 0)->decrement('used_count');
            $usage->delete();
        }
    }
}
