<?php

namespace App\Models\Shop;

use App\Models\Concerns\HasMediaUrls;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasMediaUrls;

    protected $fillable = [
        'product_id', 'label', 'option_value_ids', 'sku', 'price', 'sale_price', 'stock', 'image', 'weight', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'option_value_ids' => 'array',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function basePrice(?Product $product = null): float
    {
        $product ??= $this->product;

        return (float) ($this->price ?? $product->price);
    }

    /** Effective unit price, following the product's sale window. */
    public function currentPrice(?Product $product = null): float
    {
        $product ??= $this->product;

        if ($this->price === null) {
            return $product->currentPrice();
        }

        return $product->saleWindowActive() && $this->sale_price !== null && (float) $this->sale_price < (float) $this->price
            ? (float) $this->sale_price
            : (float) $this->price;
    }

    public function availableStock(): int
    {
        return max(0, $this->stock - $this->reserved_stock);
    }
}
