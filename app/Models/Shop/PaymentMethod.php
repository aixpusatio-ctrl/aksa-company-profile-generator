<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    public const TYPES = ['bank_transfer' => 'Transfer bank manual', 'cod' => 'Bayar di tempat (COD)'];

    protected $fillable = ['company_profile_id', 'provider', 'type', 'name', 'instructions', 'config', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['config' => 'array', 'is_active' => 'boolean'];
    }
}
