<?php

namespace App\Support\Website;

/**
 * Turns branding settings into CSS custom properties + font URLs.
 */
class Brand
{
    public static function cssVariables(array $brand): string
    {
        $primary = self::color($brand['primary_color'] ?? '#1d4ed8');
        $secondary = self::color($brand['secondary_color'] ?? '#0f172a');
        $radius = config('website-templates.radii.'.($brand['border_radius'] ?? 'md'), '8px');
        $buttonRadius = match ($brand['button_style'] ?? 'rounded') {
            'pill' => '9999px',
            'square' => '0px',
            default => $radius === '0px' ? '6px' : $radius,
        };

        $vars = [
            '--brand-primary' => $primary,
            '--brand-secondary' => $secondary,
            '--brand-on-primary' => self::contrast($primary),
            '--brand-on-secondary' => self::contrast($secondary),
            '--brand-font-heading' => self::fontStack($brand['heading_font'] ?? 'Inter'),
            '--brand-font-body' => self::fontStack($brand['body_font'] ?? 'Inter'),
            '--brand-radius' => $radius,
            '--brand-btn-radius' => $buttonRadius,
        ];

        return collect($vars)->map(fn ($value, $key) => $key.':'.$value)->implode(';');
    }

    public static function fontsUrl(array $brand, array $extra = []): ?string
    {
        $registry = config('website-templates.font_registry');
        $fonts = collect([$brand['heading_font'] ?? null, $brand['body_font'] ?? null, ...$extra])
            ->filter(fn ($font) => isset($registry[$font]))
            ->unique()
            ->map(fn ($font) => 'family='.str_replace(' ', '+', $font).':wght@'.$registry[$font][0]);

        return $fonts->isEmpty() ? null : 'https://fonts.googleapis.com/css2?'.$fonts->implode('&').'&display=swap';
    }

    public static function color(?string $value): string
    {
        return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) ? strtolower($value) : '#1d4ed8';
    }

    /** Readable text color (#fff or #111) on top of the given background. */
    public static function contrast(string $hex): string
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.6 ? '#111111' : '#ffffff';
    }

    private static function fontStack(string $font): string
    {
        $registry = config('website-templates.font_registry');
        $font = isset($registry[$font]) ? $font : 'Inter';

        return "'{$font}',".match ($registry[$font][1]) {
            'serif' => 'Georgia,serif',
            'mono' => 'ui-monospace,SFMono-Regular,Menlo,monospace',
            default => 'ui-sans-serif,system-ui,sans-serif',
        };
    }
}
