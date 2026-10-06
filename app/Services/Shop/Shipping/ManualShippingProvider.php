<?php

namespace App\Services\Shop\Shipping;

use App\Models\Shop\ShippingMethod;
use Illuminate\Support\Str;

/**
 * Seller-configured shipping:
 *  - pickup: free, no address needed
 *  - flat:   fixed cost (optionally free above min_order)
 *  - free:   free (optionally only above min_order)
 *  - custom: per-city rates and/or per-kg rate from config
 *            {"base": 10000, "per_kg": 5000, "cities": {"Jakarta": 15000}}
 */
class ManualShippingProvider implements ShippingProviderInterface
{
    public function key(): string
    {
        return 'manual';
    }

    public function quote(ShippingMethod $method, ShippingContext $context): ?ShippingQuote
    {
        $minOrder = $method->min_order !== null ? (float) $method->min_order : null;

        $cost = match ($method->type) {
            'pickup' => 0.0,
            'free' => ($minOrder === null || $context->subtotal >= $minOrder) ? 0.0 : null,
            'flat' => ($minOrder !== null && $context->subtotal >= $minOrder) ? 0.0 : (float) $method->cost,
            'custom' => $this->customRate($method, $context),
            default => null,
        };

        if ($cost === null) {
            return null;
        }

        if ($context->freeShippingCoupon && $method->type !== 'pickup') {
            $cost = 0.0;
        }

        return new ShippingQuote(
            methodId: $method->id,
            provider: $this->key(),
            type: $method->type,
            name: $method->name,
            cost: $cost,
            description: $method->description,
            estimate: $method->estimate,
            requiresAddress: $method->type !== 'pickup',
        );
    }

    private function customRate(ShippingMethod $method, ShippingContext $context): ?float
    {
        $config = $method->config ?? [];
        $cities = collect($config['cities'] ?? [])->mapWithKeys(fn ($rate, $city) => [Str::lower(trim($city)) => (float) $rate]);

        $base = $context->city && $cities->has(Str::lower(trim($context->city)))
            ? $cities[Str::lower(trim($context->city))]
            : (float) ($config['base'] ?? $method->cost);

        $perKg = (float) ($config['per_kg'] ?? 0);
        $kg = max(1, (int) ceil($context->weightGrams / 1000));

        return $base + ($perKg > 0 ? $perKg * $kg : 0);
    }
}
