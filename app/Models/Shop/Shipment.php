<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = ['order_id', 'provider', 'method', 'cost', 'courier', 'tracking_number', 'status', 'shipped_at', 'delivered_at'];

    protected function casts(): array
    {
        return ['cost' => 'decimal:2', 'shipped_at' => 'datetime', 'delivered_at' => 'datetime'];
    }
}
