<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductTag extends Model
{
    /** Built-in tags created for every shop. */
    public const DEFAULTS = ['New' => 'sky', 'Best Seller' => 'amber', 'Sale' => 'rose', 'Featured' => 'violet', 'Popular' => 'emerald'];

    public const COLORS = ['slate', 'sky', 'amber', 'rose', 'violet', 'emerald'];

    protected $fillable = ['company_profile_id', 'name', 'slug', 'color'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag_relations');
    }
}
