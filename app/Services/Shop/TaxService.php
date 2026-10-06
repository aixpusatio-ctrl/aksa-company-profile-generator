<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Tax;

/**
 * Applies the shop's active tax (if any). Nothing is hard-coded: sellers
 * define name, rate and whether prices already include the tax.
 */
class TaxService
{
    public function active(CompanyProfile $company): ?Tax
    {
        return $company->taxes()->where('is_active', true)->latest('id')->first();
    }

    /**
     * @return array{amount: float, name: ?string, inclusive: bool, rate: float}
     */
    public function calculate(?Tax $tax, float $taxable): array
    {
        if (! $tax || $taxable <= 0) {
            return ['amount' => 0.0, 'name' => $tax?->name, 'inclusive' => (bool) $tax?->inclusive, 'rate' => (float) ($tax?->rate ?? 0)];
        }

        $rate = (float) $tax->rate / 100;
        $amount = $tax->inclusive ? $taxable - ($taxable / (1 + $rate)) : $taxable * $rate;

        return ['amount' => round($amount, 2), 'name' => $tax->name, 'inclusive' => $tax->inclusive, 'rate' => (float) $tax->rate];
    }
}
