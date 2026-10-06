<?php

namespace App\Models\Shop;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class ShopSetting extends Model
{
    /** Default shop options (merged with stored values). */
    public const DEFAULT_OPTIONS = [
        // Product call-to-actions (can be overridden per product)
        'cta_add_to_cart' => true,
        'cta_buy_now' => true,
        'cta_whatsapp' => true,
        'cta_contact' => false,
        // Checkout
        'whatsapp_checkout' => true,
        'guest_checkout' => true,
        'require_phone' => true,
        'min_order' => null,
        'order_note' => true,
        // Inventory
        'allow_backorder' => false,
        'show_stock' => true,
        // Reviews
        'reviews_enabled' => true,
        'reviews_moderation' => true,
        'reviews_require_purchase' => false,
        // Storefront
        'shop_title' => null,
        'shop_subtitle' => null,
        'banner_image' => null,
        'products_per_page' => 12,
    ];

    /** Shop homepage sections (key => label) in default order. */
    public const SECTIONS = [
        'hero' => 'Shop Hero',
        'featured' => 'Featured Products',
        'categories' => 'Categories',
        'best_sellers' => 'Best Sellers',
        'new' => 'New Products',
        'sale' => 'Sale Products',
        'brands' => 'Brands',
        'testimonials' => 'Testimonials',
        'newsletter' => 'Newsletter',
    ];

    protected $fillable = ['company_profile_id', 'name', 'description', 'currency', 'order_prefix', 'low_stock_threshold', 'options', 'sections'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'sections' => 'array',
            'low_stock_threshold' => 'integer',
        ];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function option(string $key, mixed $default = null): mixed
    {
        return Arr::get(array_merge(self::DEFAULT_OPTIONS, $this->options ?? []), $key, $default);
    }

    public function options(): array
    {
        return array_merge(self::DEFAULT_OPTIONS, $this->options ?? []);
    }

    /**
     * Shop homepage sections in order: [['key' => 'hero', 'enabled' => true], ...]
     */
    public function sectionList(): array
    {
        $stored = collect($this->sections ?? [])->filter(fn ($s) => isset(self::SECTIONS[$s['key'] ?? '']))->keyBy('key');
        $order = $stored->keys()->merge(array_keys(self::SECTIONS))->unique();

        return $order->map(fn ($key) => [
            'key' => $key,
            'label' => self::SECTIONS[$key],
            'enabled' => (bool) ($stored[$key]['enabled'] ?? ! in_array($key, ['brands', 'newsletter'], true)),
        ])->values()->all();
    }

    public function enabledSections(): array
    {
        return collect($this->sectionList())->where('enabled', true)->pluck('key')->all();
    }

    public function displayName(): string
    {
        return $this->name ?: ($this->companyProfile?->name ?? 'Shop');
    }
}
