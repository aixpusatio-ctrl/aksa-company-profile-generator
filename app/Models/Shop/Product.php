<?php

namespace App\Models\Shop;

use App\Models\CompanyProfile;
use App\Support\MediaUrl;
use App\Support\Shop\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_ARCHIVED];

    public const STOCK_STATUSES = ['in_stock' => 'In stock', 'out_of_stock' => 'Out of stock', 'backorder' => 'Pre-order / backorder'];

    /** Call-to-action keys (enabled by shop settings, overridable per product). */
    public const CTAS = ['add_to_cart', 'buy_now', 'whatsapp', 'contact'];

    protected $fillable = [
        'company_profile_id', 'category_id', 'name', 'slug', 'sku', 'brand', 'short_description', 'description',
        'specifications', 'price', 'compare_price', 'sale_price', 'sale_starts_at', 'sale_ends_at', 'cost_price',
        'track_stock', 'stock', 'low_stock_threshold', 'stock_status', 'weight', 'status', 'featured', 'cta',
        'related_ids', 'seo_title', 'seo_description', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'cta' => 'array',
            'related_ids' => 'array',
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'rating_avg' => 'decimal:2',
            'track_stock' => 'boolean',
            'featured' => 'boolean',
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'sale_starts_at' => 'datetime',
            'sale_ends_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    // ------------------------------------------------------------ Relations

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order')->orderBy('id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag_relations');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // ------------------------------------------------------------ Scopes

    /** Visible on the storefront. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeOnSale(Builder $query): Builder
    {
        $now = now();

        return $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')
            ->where(fn ($q) => $q->whereNull('sale_starts_at')->orWhere('sale_starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>=', $now));
    }

    // ------------------------------------------------------------ Pricing

    /** Sale window currently running (ignores whether a sale price exists). */
    public function saleWindowActive(): bool
    {
        return (! $this->sale_starts_at || $this->sale_starts_at->isPast())
            && (! $this->sale_ends_at || $this->sale_ends_at->isFuture());
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price && $this->saleWindowActive();
    }

    /** Price the customer pays (before coupons/tax). */
    public function currentPrice(): float
    {
        return (float) ($this->isOnSale() ? $this->sale_price : $this->price);
    }

    /** Struck-through reference price, if any. */
    public function originalPrice(): ?float
    {
        if ($this->isOnSale()) {
            return (float) $this->price;
        }

        return $this->compare_price !== null && (float) $this->compare_price > (float) $this->price ? (float) $this->compare_price : null;
    }

    public function discountPercent(): ?int
    {
        $original = $this->originalPrice();

        return $original ? (int) round((1 - $this->currentPrice() / $original) * 100) : null;
    }

    /** Lowest–highest current price across active variants. */
    public function priceRange(): array
    {
        $prices = $this->relationLoaded('variants') && $this->variants->where('is_active', true)->isNotEmpty()
            ? $this->variants->where('is_active', true)->map(fn (ProductVariant $v) => $v->currentPrice($this))
            : collect([$this->currentPrice()]);

        return [(float) $prices->min(), (float) $prices->max()];
    }

    public function formattedPrice(): string
    {
        [$min, $max] = $this->priceRange();

        return $min === $max ? Money::format($min) : Money::format($min).' – '.Money::format($max);
    }

    // ------------------------------------------------------------ Stock

    public function hasVariants(): bool
    {
        return $this->relationLoaded('variants') ? $this->variants->isNotEmpty() : $this->variants()->exists();
    }

    public function availableStock(): int
    {
        if ($this->hasVariants()) {
            return (int) $this->variants->where('is_active', true)->sum(fn (ProductVariant $v) => $v->availableStock());
        }

        return max(0, $this->stock - $this->reserved_stock);
    }

    public function isInStock(): bool
    {
        if ($this->stock_status === 'out_of_stock') {
            return false;
        }

        return ! $this->track_stock || $this->stock_status === 'backorder' || $this->availableStock() > 0;
    }

    public function isLowStock(int $threshold): bool
    {
        $threshold = $this->low_stock_threshold ?? $threshold;
        $available = $this->availableStock();

        return $this->track_stock && $available > 0 && $available <= $threshold;
    }

    public function availabilityLabel(): string
    {
        return match (true) {
            ! $this->isInStock() => 'Stok habis',
            $this->stock_status === 'backorder' && $this->availableStock() <= 0 => 'Pre-order',
            default => 'Tersedia',
        };
    }

    // ------------------------------------------------------------ Media & misc

    public function mainImage(): ?string
    {
        $image = $this->relationLoaded('images') ? $this->images->first() : $this->images()->first();

        return $image ? MediaUrl::resolve($image->image) : null;
    }

    public function secondImage(): ?string
    {
        $image = $this->relationLoaded('images') ? $this->images->get(1) : null;

        return $image ? MediaUrl::resolve($image->image) : null;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /** Whether a call-to-action is enabled (shop default + product override). */
    public function ctaEnabled(string $cta, ShopSetting $settings): bool
    {
        $override = $this->cta[$cta] ?? null;

        return $override === null ? (bool) $settings->option('cta_'.$cta, false) : (bool) $override;
    }

    public function isNew(): bool
    {
        return ($this->published_at ?? $this->created_at)?->gt(now()->subDays(30)) ?? false;
    }
}
