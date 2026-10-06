<?php

namespace App\Models\Shop;

use App\Support\Shop\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    public const TYPES = ['percentage' => 'Persentase', 'fixed' => 'Potongan nominal', 'free_shipping' => 'Gratis ongkir'];

    protected $fillable = [
        'company_profile_id', 'code', 'description', 'type', 'value', 'min_purchase', 'max_discount',
        'usage_limit', 'usage_limit_per_customer', 'starts_at', 'ends_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function label(): string
    {
        return match ($this->type) {
            'percentage' => rtrim(rtrim(number_format((float) $this->value, 2, ',', '.'), '0'), ',').'% OFF',
            'free_shipping' => 'Gratis ongkir',
            default => Money::format((float) $this->value).' OFF',
        };
    }
}
