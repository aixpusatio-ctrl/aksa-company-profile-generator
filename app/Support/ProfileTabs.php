<?php

namespace App\Support;

use App\Models\CompanyProfile;
use App\Services\MediaService;
use Illuminate\Validation\Rule;

/**
 * Form definitions for the company profile editor tabs (also used by the
 * creation wizard and the autosave endpoint).
 */
class ProfileTabs
{
    public const TABS = [
        'info' => ['label' => 'Company Information', 'icon' => 'building'],
        'about' => ['label' => 'About', 'icon' => 'document'],
        'contact' => ['label' => 'Contact', 'icon' => 'phone'],
        'branding' => ['label' => 'Branding', 'icon' => 'paint'],
        'seo' => ['label' => 'SEO', 'icon' => 'search'],
    ];

    /** Image fields per tab and the media collection they are stored in. */
    public const IMAGES = [
        'info' => ['logo' => 'logos', 'favicon' => 'logos'],
        'branding' => ['logo' => 'logos', 'favicon' => 'logos', 'hero_image' => 'images'],
        'seo' => ['og_image' => 'images'],
    ];

    public static function exists(string $tab): bool
    {
        return array_key_exists($tab, self::TABS);
    }

    public static function rules(string $tab, CompanyProfile $company): array
    {
        $rules = match ($tab) {
            'info' => [
                'name' => ['required', 'string', 'max:150'],
                'tagline' => ['nullable', 'string', 'max:200'],
                'description' => ['nullable', 'string', 'max:2000'],
                'established_year' => ['nullable', 'integer', 'min:1800', 'max:'.(date('Y') + 1)],
                'phone' => ['nullable', 'string', 'max:40'],
                'email' => ['nullable', 'email', 'max:150'],
                'whatsapp' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s()]+$/'],
                'website' => ['nullable', 'url:http,https', 'max:255'],
                'social_links' => ['nullable', 'array'],
                'social_links.*' => ['nullable', 'url:http,https', 'max:255'],
            ],
            'about' => [
                'about' => ['nullable', 'string', 'max:20000'],
                'vision' => ['nullable', 'string', 'max:2000'],
                'mission' => ['nullable', 'string', 'max:10000'],
                'history' => ['nullable', 'string', 'max:20000'],
                'company_values' => ['nullable', 'string', 'max:10000'],
                'highlights' => ['nullable', 'array', 'max:6'],
                'highlights.*.value' => ['nullable', 'string', 'max:20'],
                'highlights.*.label' => ['nullable', 'string', 'max:60'],
            ],
            'contact' => [
                'address' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:100'],
                'province' => ['nullable', 'string', 'max:100'],
                'country' => ['nullable', 'string', 'max:100'],
                'postal_code' => ['nullable', 'string', 'max:20'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'google_maps_url' => ['nullable', 'url:https', 'max:1000'],
                'working_hours' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:40'],
                'email' => ['nullable', 'email', 'max:150'],
                'whatsapp' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s()]+$/'],
            ],
            'branding' => [
                'branding' => ['nullable', 'array'],
                'branding.primary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'branding.secondary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'branding.heading_font' => ['nullable', Rule::in(config('website-templates.fonts'))],
                'branding.body_font' => ['nullable', Rule::in(config('website-templates.fonts'))],
                'branding.button_style' => ['nullable', Rule::in(array_keys(config('website-templates.button_styles')))],
                'branding.border_radius' => ['nullable', Rule::in(array_keys(config('website-templates.radii')))],
                'hero_video' => ['nullable', 'url:https', 'max:500', 'regex:/(\.(mp4|webm)(\?.*)?$)|youtube\.com|youtu\.be|vimeo\.com/i'],
            ],
            'seo' => [
                'seo_title' => ['nullable', 'string', 'max:120'],
                'seo_description' => ['nullable', 'string', 'max:500'],
                'seo_keywords' => ['nullable', 'string', 'max:255'],
                'og_title' => ['nullable', 'string', 'max:120'],
                'og_description' => ['nullable', 'string', 'max:500'],
            ],
            default => abort(404),
        };

        foreach (array_keys(self::IMAGES[$tab] ?? []) as $field) {
            $rules[$field] = ['nullable', MediaService::imageRule()];
            $rules[$field.'_media'] = ['nullable', 'string', 'max:255'];
            $rules[$field.'_remove'] = ['nullable', 'boolean'];
        }

        return $rules;
    }

    /**
     * Rules for autosave requests (partial, text-only).
     */
    public static function autosaveRules(string $tab, CompanyProfile $company): array
    {
        return collect(self::rules($tab, $company))
            ->reject(fn ($rule, $field) => array_key_exists($field, self::IMAGES[$tab] ?? []) || str_ends_with($field, '_media') || str_ends_with($field, '_remove'))
            ->map(fn ($rules) => array_merge(['sometimes'], array_values(array_filter($rules, fn ($r) => $r !== 'required'))))
            ->all();
    }
}
