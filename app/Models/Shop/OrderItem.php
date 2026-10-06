<?php

namespace App\Models\Shop;

use App\Models\Concerns\HasMediaUrls;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Snapshot of a purchased product: later product changes never alter order history.
 */
class OrderItem extends Model
{
    use HasMediaUrls;

    protected $fillable = ['order_id', 'product_id', 'product_variant_id', 'product_name', 'variant_label', 'sku', 'image', 'price', 'quantity', 'subtotal', 'metadata'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'subtotal' => 'decimal:2', 'quantity' => 'integer', 'metadata' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
