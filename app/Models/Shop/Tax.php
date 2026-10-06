<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

/**
 * A configurable tax (name, rate, inclusive/exclusive). No specific tax is
 * hard-coded; the active tax of a shop is applied at checkout.
 */
class Tax extends Model
{
    protected $fillable = ['company_profile_id', 'name', 'rate', 'inclusive', 'is_active'];

    protected function casts(): array
    {
        return ['rate' => 'decimal:2', 'inclusive' => 'boolean', 'is_active' => 'boolean'];
    }
}
