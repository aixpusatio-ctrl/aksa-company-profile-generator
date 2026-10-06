<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    public const TYPES = ['pickup' => 'Ambil di toko (Pickup)', 'flat' => 'Flat rate', 'free' => 'Gratis ongkir', 'custom' => 'Custom (per kota / per kg)'];

    protected $fillable = ['company_profile_id', 'provider', 'type', 'name', 'description', 'estimate', 'cost', 'min_order', 'config', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['cost' => 'decimal:2', 'min_order' => 'decimal:2', 'config' => 'array', 'is_active' => 'boolean'];
    }
}
