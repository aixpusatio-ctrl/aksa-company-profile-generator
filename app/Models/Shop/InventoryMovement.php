<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    public const UPDATED_AT = null;

    public const TYPES = ['adjustment' => 'Penyesuaian', 'reserve' => 'Reservasi', 'release' => 'Batal reservasi', 'sale' => 'Terjual', 'return' => 'Retur'];

    protected $fillable = ['company_profile_id', 'product_id', 'product_variant_id', 'type', 'quantity', 'stock_after', 'reserved_after', 'reason', 'reference', 'user_id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
