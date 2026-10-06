<?php

namespace App\Services\Shop\Shipping;

use App\Models\Shop\ShippingMethod;

/**
 * A shipping provider calculates rates for the shipping methods it owns.
 * The manual provider covers pickup / flat / free / custom rates; providers
 * for Indonesian couriers (e.g. RajaOngkir, Biteship) can implement this
 * interface and be registered in ShippingService without touching checkout.
 */
interface ShippingProviderInterface
{
    /** Provider key stored in shipping_methods.provider. */
    public function key(): string;

    /**
     * Quote a method for the given context, or null when unavailable.
     */
    public function quote(ShippingMethod $method, ShippingContext $context): ?ShippingQuote;
}
