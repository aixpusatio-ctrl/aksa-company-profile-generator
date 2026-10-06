<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'provider', 'method', 'amount', 'status', 'reference', 'instructions', 'metadata', 'paid_at'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'instructions' => 'array', 'metadata' => 'array', 'paid_at' => 'datetime'];
    }
}
