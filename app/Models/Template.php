<?php

namespace App\Models;

use App\Models\Concerns\HasMediaUrls;
use App\Support\Website\DesignSystem;
use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    /** @use HasFactory<TemplateFactory> */
    use HasFactory, HasMediaUrls;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'template_category_id', 'name', 'slug', 'layout', 'demo', 'description', 'style', 'thumbnail',
        'mobile_thumbnail', 'preview_url', 'status', 'is_featured', 'settings', 'config', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'config' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class, 'template_category_id');
    }

    public function companyProfiles(): HasMany
    {
        return $this->hasMany(CompanyProfile::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Layout defaults merged with this template's overrides.
     */
    public function resolvedSettings(): array
    {
        $defaults = config("website-templates.layouts.{$this->layout}.defaults", []);

        return array_merge($defaults, array_filter($this->settings ?? [], fn ($v) => $v !== null && $v !== ''));
    }

    /** Built from the component library (vs. a hand-crafted Blade theme). */
    public function isComposed(): bool
    {
        return $this->layout === 'composer';
    }

    /**
     * Design system (components + design tokens) of a composed template.
     */
    public function designSystem(): DesignSystem
    {
        $config = $this->config ?? [];

        return new DesignSystem($config['design'] ?? [], $config['components'] ?? []);
    }

    /** Style keywords as an array ("Enterprise · Clean" → ['Enterprise', 'Clean']). */
    public function styleTags(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[·,]/u', (string) $this->style))));
    }

    /** Demo dataset key used for previews. */
    public function demoKey(): string
    {
        return $this->demo ?: $this->layout;
    }

    public function layoutName(): string
    {
        return config("website-templates.layouts.{$this->layout}.name", $this->layout);
    }
}
