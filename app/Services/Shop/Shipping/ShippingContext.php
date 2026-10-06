<?php

namespace App\Services\Shop\Shipping;

/**
 * Everything a provider may need to compute a rate.
 */
final class ShippingContext
{
    public function __construct(
        public readonly float $subtotal,
        public readonly int $weightGrams,
        public readonly int $quantity,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $postalCode = null,
        public readonly bool $freeShippingCoupon = false,
    ) {}
}
