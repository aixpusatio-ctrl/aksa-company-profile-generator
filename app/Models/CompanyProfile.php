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
        'template_id', 'name', 'slug', 'tagline', 'description', 'logo', 'favicon', 'hero_image',
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
