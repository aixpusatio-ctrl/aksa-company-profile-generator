<?php

namespace App\Models\Shop;

use App\Models\CompanyProfile;
use App\Models\Concerns\HasMediaUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasMediaUrls;

    protected $fillable = ['company_profile_id', 'parent_id', 'name', 'slug', 'description', 'image', 'status', 'sort_order'];

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** Ids of this category and all of its descendants. */
    public function descendantIds(): array
    {
        $ids = [$this->id];
        $frontier = [$this->id];
        $all = ProductCategory::query()->where('company_profile_id', $this->company_profile_id)->get(['id', 'parent_id']);

        while ($frontier) {
            $children = $all->whereIn('parent_id', $frontier)->pluck('id')->all();
            $children = array_diff($children, $ids);
            $ids = array_merge($ids, $children);
            $frontier = $children;
        }

        return $ids;
    }
}
