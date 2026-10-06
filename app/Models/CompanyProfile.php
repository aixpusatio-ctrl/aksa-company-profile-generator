<?php

namespace App\Models;

use App\Models\Concerns\HasMediaUrls;
use Database\Factories\CompanyProfileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyProfile extends Model
{
    /** @use HasFactory<CompanyProfileFactory> */
    use HasFactory, HasMediaUrls;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const SOCIAL_NETWORKS = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'x' => 'X',
    ];

    protected $fillable = [
        'template_id', 'name', 'slug', 'tagline', 'description', 'logo', 'favicon', 'hero_image', 'hero_video', 'highlights',
        'established_year', 'phone', 'email', 'whatsapp', 'address', 'city', 'province', 'country',
        'postal_code', 'latitude', 'longitude', 'google_maps_url', 'website', 'working_hours',
        'social_links', 'about', 'vision', 'mission', 'history', 'company_values', 'branding',
        'seo_title', 'seo_description', 'seo_keywords', 'og_title', 'og_description', 'og_image',
        'wizard_step', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'highlights' => 'array',
            'shop_enabled' => 'boolean',
            'branding' => 'array',
            'published_at' => 'datetime',
            'established_year' => 'integer',
            'wizard_step' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // ---------------------------------------------------------------- Relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CompanySection::class)->orderBy('sort_order');
    }

    public function services(): HasMany
    {
        return $this->hasMany(CompanyService::class)->ordered();
    }

    public function products(): HasMany
    {
        return $this->hasMany(CompanyProduct::class)->ordered();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(CompanyProject::class)->ordered();
    }

    public function team(): HasMany
    {
        return $this->hasMany(CompanyTeamMember::class)->ordered();
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(CompanyTestimonial::class)->ordered();
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(CompanyGalleryItem::class)->ordered();
    }

    public function pages(): HasMany
    {
        return $this->hasMany(CompanyPage::class)->ordered();
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class)->orderBy('sort_order')->orderBy('id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function primaryDomain(): HasOne
    {
        return $this->hasOne(Domain::class)->where('status', Domain::STATUS_ACTIVE)->orderByDesc('is_primary')->oldest();
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class)->latest();
    }

    /** Alias of contactMessages() used for scoped route bindings. */
    public function messages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class);
    }

    // ---------------------------------------------------------------- Shop

    public function shopSetting(): HasOne
    {
        return $this->hasOne(Shop\ShopSetting::class);
    }

    public function shopProducts(): HasMany
    {
        return $this->hasMany(Shop\Product::class);
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(Shop\ProductCategory::class)->orderBy('sort_order')->orderBy('name');
    }

    public function productTags(): HasMany
    {
        return $this->hasMany(Shop\ProductTag::class)->orderBy('name');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Shop\Order::class)->latest();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Shop\Customer::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Shop\Coupon::class)->latest();
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Shop\Tax::class);
    }

    public function shippingMethods(): HasMany
    {
        return $this->hasMany(Shop\ShippingMethod::class)->orderBy('sort_order')->orderBy('id');
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(Shop\PaymentMethod::class)->orderBy('sort_order')->orderBy('id');
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(Shop\Review::class)->latest();
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(Shop\InventoryMovement::class)->latest('created_at');
    }

    public function hasShop(): bool
    {
        return (bool) $this->shop_enabled;
    }

    // ---------------------------------------------------------------- Scopes

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    // ---------------------------------------------------------------- Helpers

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function subdomainHost(): string
    {
        return $this->slug.'.'.config('platform.domain');
    }

    /**
     * Host name visitors should use: the primary active custom domain when
     * available, otherwise the platform sub domain.
     */
    public function primaryHost(): string
    {
        return $this->primaryDomain?->domain ?? $this->subdomainHost();
    }

    public function publicUrl(?string $host = null): string
    {
        $host ??= $this->primaryHost();
        $port = config('platform.port');
        $isCustom = $host !== $this->subdomainHost();

        return config('platform.scheme').'://'.$host.($port && ! $isCustom ? ':'.$port : '');
    }

    public function subdomainUrl(): string
    {
        return $this->publicUrl($this->subdomainHost());
    }

    /**
     * Template defaults merged with the company's own branding overrides.
     */
    public function brand(): array
    {
        $defaults = $this->template?->resolvedSettings()
            ?? config('website-templates.layouts.corporate.defaults');

        $overrides = array_filter($this->branding ?? [], fn ($value) => $value !== null && $value !== '');

        return array_merge($defaults, $overrides);
    }

    public function layout(): string
    {
        $layout = $this->template?->layout;

        return $layout && config("website-templates.layouts.{$layout}") ? $layout : 'corporate';
    }

    public function isComposed(): bool
    {
        return $this->layout() === 'composer';
    }

    public function socialLinks(): array
    {
        return array_filter($this->social_links ?? [], fn ($url) => filled($url));
    }

    public function whatsappUrl(): ?string
    {
        if (blank($this->whatsapp)) {
            return null;
        }

        $number = preg_replace('/\D+/', '', $this->whatsapp);

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return 'https://wa.me/'.$number;
    }

    public function fullAddress(): string
    {
        return collect([$this->address, $this->city, $this->province, $this->postal_code, $this->country])
            ->filter()
            ->implode(', ');
    }

    /**
     * URL safe for embedding in an iframe (Google Maps embed).
     */
    public function mapEmbedUrl(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return 'https://maps.google.com/maps?q='.$this->latitude.','.$this->longitude.'&z=15&output=embed';
        }

        $query = $this->fullAddress();

        return $query ? 'https://maps.google.com/maps?q='.urlencode($query).'&z=15&output=embed' : null;
    }

    /**
     * Key figures for stats sections: the company's own highlights, or
     * figures derived from its real data when none were entered.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function stats(): array
    {
        $own = collect($this->highlights ?? [])
            ->filter(fn ($item) => filled($item['value'] ?? null) && filled($item['label'] ?? null))
            ->map(fn ($item) => ['value' => (string) $item['value'], 'label' => (string) $item['label']])
            ->values();

        if ($own->isNotEmpty()) {
            return $own->take(6)->all();
        }

        return collect([
            $this->established_year ? ['value' => (date('Y') - $this->established_year).'+', 'label' => 'Tahun pengalaman'] : null,
            $this->projects->isNotEmpty() ? ['value' => (string) $this->projects->count(), 'label' => 'Proyek unggulan'] : null,
            $this->services->isNotEmpty() ? ['value' => (string) $this->services->count(), 'label' => 'Layanan'] : null,
            $this->team->isNotEmpty() ? ['value' => (string) $this->team->count(), 'label' => 'Pimpinan & ahli'] : null,
            $this->clients() ? ['value' => count($this->clients()).'+', 'label' => 'Klien & mitra'] : null,
        ])->filter()->values()->take(4)->all();
    }

    /**
     * Client / partner names taken from projects and testimonials.
     *
     * @return array<int, string>
     */
    public function clients(): array
    {
        return collect($this->projects->pluck('client'))
            ->merge($this->testimonials->pluck('company'))
            ->filter()
            ->map(fn ($name) => trim($name))
            ->unique()
            ->values()
            ->all();
    }

    /** 'file' (mp4/webm), 'youtube', 'vimeo' or null. */
    public function heroVideoType(): ?string
    {
        $url = (string) $this->hero_video;

        return match (true) {
            $url === '' => null,
            (bool) preg_match('/\.(mp4|webm)(\?.*)?$/i', $url) => 'file',
            str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be') => 'youtube',
            str_contains($url, 'vimeo.com') => 'vimeo',
            default => null,
        };
    }

    /** Embeddable player URL for YouTube / Vimeo hero videos. */
    public function heroVideoEmbedUrl(): ?string
    {
        $url = (string) $this->hero_video;

        if ($this->heroVideoType() === 'youtube' && preg_match('~(?:v=|youtu\.be/|embed/)([A-Za-z0-9_-]{11})~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1].'?autoplay=1&rel=0';
        }

        if ($this->heroVideoType() === 'vimeo' && preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1].'?autoplay=1';
        }

        return null;
    }

    public function missionItems(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', strip_tags(str_replace(['</li>', '</p>', '<br>', '<br/>'], "\n", (string) $this->mission))))
            ->map(fn ($line) => trim(ltrim(trim($line), '-•*0123456789. ')))
            ->filter()
            ->values()
            ->all();
    }

    public function valueItems(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', strip_tags(str_replace(['</li>', '</p>', '<br>', '<br/>'], "\n", (string) $this->company_values))))
            ->map(fn ($line) => trim(ltrim(trim($line), '-•*0123456789. ')))
            ->filter()
            ->values()
            ->all();
    }
}
