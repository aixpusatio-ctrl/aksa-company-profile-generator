<?php

namespace App\Support\Website;

use Illuminate\Support\HtmlString;

/**
 * Per-template design system for "composer" templates.
 *
 * A template stores design tokens (theme, typography preset, scale, spacing,
 * card style, eyebrow style, section rhythm, animation, buttons, images).
 * Components in resources/views/components/company/* never hard-code those
 * decisions: they ask this object for classes, so the same component looks
 * different under different templates, while different component variants
 * provide different layouts.
 */
class DesignSystem
{
    /** Allowed values per token (first value = default). */
    public const OPTIONS = [
        'theme' => ['light', 'soft', 'cream', 'sand', 'mono', 'dark', 'midnight', 'graphite'],
        'heading' => ['sans-tight', 'sans-bold', 'sans-light', 'serif', 'serif-light', 'display-upper', 'condensed', 'mono'],
        'scale' => ['lg', 'md', 'xl', 'xxl'],
        'spacing' => ['normal', 'compact', 'airy'],
        'container' => ['normal', 'narrow', 'wide', 'full'],
        'card' => ['bordered', 'shadow', 'flat', 'glass', 'elevated', 'outline', 'soft', 'plain'],
        'eyebrow' => ['plain', 'pill', 'line', 'number', 'mono', 'dot', 'bracket', 'none'],
        'align' => ['left', 'center'],
        'rhythm' => ['alternate', 'flat', 'contrast', 'accent', 'bands'],
        'animation' => ['slide', 'fade', 'zoom', 'reveal', 'none'],
        'button' => ['solid', 'outline', 'underline', 'arrow'],
        'image' => ['rounded', 'square', 'arch', 'soft'],
        'divider' => ['none', 'line'],
        // Online shop
        'product_card' => ['classic', 'minimal', 'luxury', 'bento', 'horizontal', 'image', 'compact', 'modern'],
        'cart' => ['drawer', 'page'],
        'shop_hero' => ['banner', 'split', 'minimal', 'image'],
    ];

    public const LABELS = [
        'theme' => 'Surface theme',
        'heading' => 'Typography preset',
        'scale' => 'Heading scale',
        'spacing' => 'Section spacing',
        'container' => 'Container width',
        'card' => 'Card style',
        'eyebrow' => 'Section label style',
        'align' => 'Heading alignment',
        'rhythm' => 'Section rhythm',
        'animation' => 'Animation',
        'button' => 'Button style',
        'image' => 'Image shape',
        'divider' => 'Section dividers',
        'product_card' => 'Shop: product card',
        'cart' => 'Shop: cart style',
        'shop_hero' => 'Shop: hero',
    ];

    /** Slots of a composed website and their default component variant. */
    public const SLOTS = [
        'navbar' => 'classic',
        'hero' => 'split',
        'about' => 'two-column',
        'services' => 'cards',
        'products' => 'grid',
        'projects' => 'grid',
        'team' => 'grid',
        'testimonials' => 'cards',
        'gallery' => 'grid',
        'cta' => 'band',
        'contact' => 'split',
        'stats' => 'band',
        'clients' => 'logos',
        'footer' => 'classic',
        'page-header' => 'banner',
    ];

    private array $tokens;

    private array $components;

    public function __construct(array $design = [], array $components = [])
    {
        $this->tokens = [];
        foreach (self::OPTIONS as $key => $options) {
            $value = $design[$key] ?? null;
            $this->tokens[$key] = in_array($value, $options, true) ? $value : $options[0];
        }

        $this->components = [];
        foreach (self::SLOTS as $slot => $default) {
            $variant = $components[$slot] ?? null;
            $this->components[$slot] = $variant && ComponentRegistry::exists($slot, $variant) ? $variant : $default;
        }
    }

    public static function defaults(): array
    {
        return array_map(fn ($options) => $options[0], self::OPTIONS);
    }

    // ------------------------------------------------------------------ Tokens

    public function get(string $key): string
    {
        return $this->tokens[$key];
    }

    public function tokens(): array
    {
        return $this->tokens;
    }

    public function components(): array
    {
        return $this->components;
    }

    public function variant(string $slot): string
    {
        return $this->components[$slot] ?? self::SLOTS[$slot];
    }

    /** View name of the component used for a slot. */
    public function view(string $slot): string
    {
        return 'components.company.'.$slot.'.'.$this->variant($slot);
    }

    /** View of the product card used by the shop. */
    public function productCard(): string
    {
        return 'components.shop.product-card.'.$this->tokens['product_card'];
    }

    /** Design system for hand-crafted layouts (used by the shop pages). */
    public static function forCraftedLayout(string $layout): self
    {
        return new self(match ($layout) {
            'executive' => ['theme' => 'midnight', 'heading' => 'serif-light', 'card' => 'outline', 'eyebrow' => 'line', 'product_card' => 'luxury', 'cart' => 'drawer', 'shop_hero' => 'image', 'button' => 'solid', 'image' => 'square'],
            'modern-business' => ['theme' => 'soft', 'heading' => 'sans-tight', 'card' => 'shadow', 'eyebrow' => 'pill', 'product_card' => 'modern', 'cart' => 'drawer', 'shop_hero' => 'split', 'image' => 'soft'],
            'technology' => ['theme' => 'dark', 'heading' => 'sans-tight', 'card' => 'glass', 'eyebrow' => 'mono', 'product_card' => 'modern', 'cart' => 'drawer', 'shop_hero' => 'minimal'],
            'construction' => ['theme' => 'light', 'heading' => 'condensed', 'card' => 'flat', 'eyebrow' => 'line', 'product_card' => 'compact', 'cart' => 'page', 'shop_hero' => 'image', 'image' => 'square'],
            'manufacturing' => ['theme' => 'soft', 'heading' => 'sans-bold', 'card' => 'bordered', 'eyebrow' => 'number', 'product_card' => 'compact', 'cart' => 'page', 'shop_hero' => 'banner', 'image' => 'square'],
            'consulting' => ['theme' => 'cream', 'heading' => 'serif', 'card' => 'plain', 'eyebrow' => 'line', 'product_card' => 'minimal', 'cart' => 'page', 'shop_hero' => 'minimal', 'button' => 'underline', 'image' => 'square'],
            'creative-agency' => ['theme' => 'light', 'heading' => 'sans-tight', 'card' => 'flat', 'eyebrow' => 'pill', 'product_card' => 'bento', 'cart' => 'drawer', 'shop_hero' => 'split', 'image' => 'soft'],
            'professional-services' => ['theme' => 'light', 'heading' => 'serif', 'card' => 'bordered', 'eyebrow' => 'plain', 'product_card' => 'classic', 'cart' => 'page', 'shop_hero' => 'banner'],
            'minimal' => ['theme' => 'mono', 'heading' => 'sans-tight', 'card' => 'plain', 'eyebrow' => 'number', 'product_card' => 'minimal', 'cart' => 'page', 'shop_hero' => 'minimal', 'button' => 'underline', 'image' => 'square'],
            default => ['theme' => 'light', 'heading' => 'sans-bold', 'card' => 'bordered', 'eyebrow' => 'plain', 'product_card' => 'classic', 'cart' => 'drawer', 'shop_hero' => 'banner'],
        });
    }

    public function isDark(): bool
    {
        return in_array($this->tokens['theme'], ['dark', 'midnight', 'graphite'], true);
    }

    // ------------------------------------------------------------------ Page

    /** Classes for <body>: theme palette + typography + scale + spacing + animation. */
    public function bodyClass(): string
    {
        return implode(' ', [
            'theme-'.$this->tokens['theme'],
            'type-'.$this->tokens['heading'],
            'scale-'.$this->tokens['scale'],
            'space-'.$this->tokens['spacing'],
            'anim-'.$this->tokens['animation'],
            'bg-surface text-ink font-body antialiased',
        ]);
    }

    /** Horizontal container. */
    public function container(?string $size = null): string
    {
        return match ($size ?? $this->tokens['container']) {
            'narrow' => 'mx-auto w-full max-w-5xl px-5 sm:px-8',
            'wide' => 'mx-auto w-full max-w-[88rem] px-5 sm:px-8 lg:px-12',
            'full' => 'w-full px-5 sm:px-8 lg:px-14',
            'text' => 'mx-auto w-full max-w-3xl px-5 sm:px-8',
            default => 'mx-auto w-full max-w-7xl px-5 sm:px-8',
        };
    }

    // ------------------------------------------------------------------ Sections

    /**
     * Tone of the n-th content section (after the hero) according to the
     * section rhythm: base | alt | inverse.
     */
    public function tone(int $index, string $key = ''): string
    {
        return match ($this->tokens['rhythm']) {
            'flat' => 'base',
            'contrast' => $index % 3 === 2 ? 'inverse' : 'base',
            'accent' => in_array($key, ['testimonials', 'stats', 'cta'], true) ? 'inverse' : ($index % 2 ? 'alt' : 'base'),
            'bands' => ['base', 'alt', 'base', 'inverse'][$index % 4],
            default => $index % 2 ? 'alt' : 'base',
        };
    }

    /** Root classes of a <section>: background/foreground by tone + vertical rhythm. */
    public function section(string $tone = 'base', string $extra = ''): string
    {
        $toneClass = match ($tone) {
            'alt' => 'bg-surface-alt text-ink',
            'inverse' => 'tone-inverse bg-surface text-ink',
            'primary' => 'tone-primary bg-primary text-on-primary',
            'none' => 'text-ink',
            default => 'bg-surface text-ink',
        };

        $divider = $this->tokens['divider'] === 'line' && $tone !== 'inverse' ? ' border-t border-line' : '';

        return trim('relative py-section '.$toneClass.$divider.' '.$extra);
    }

    // ------------------------------------------------------------------ Elements

    /** Card surface classes for the template's card style. */
    public function card(string $extra = '', bool $hover = true): string
    {
        return trim('ds-card card-'.$this->tokens['card'].($hover ? ' ds-card-hover' : '').' '.$extra);
    }

    /** Button classes: primary | secondary | ghost | light (on dark/primary backgrounds) | link. */
    public function btn(string $kind = 'primary', string $extra = ''): string
    {
        $style = $this->tokens['button'];

        $class = match (true) {
            $kind === 'link' || ($kind === 'secondary' && in_array($style, ['underline', 'arrow'], true)) => 'ds-btn-link',
            $kind === 'primary' && $style === 'outline' => 'ds-btn ds-btn-outline-primary',
            $kind === 'primary' && $style === 'underline' => 'ds-btn ds-btn-primary ds-btn-sharp',
            $kind === 'primary' => 'ds-btn ds-btn-primary',
            $kind === 'light' => 'ds-btn ds-btn-light',
            $kind === 'ghost' => 'ds-btn ds-btn-ghost',
            default => 'ds-btn ds-btn-secondary',
        };

        return trim($class.' '.$extra);
    }

    /** Whether buttons should carry a trailing arrow icon. */
    public function arrows(): bool
    {
        return in_array($this->tokens['button'], ['arrow', 'underline'], true);
    }

    /** Image corner/shape classes. */
    public function img(string $extra = ''): string
    {
        return trim(match ($this->tokens['image']) {
            'square' => 'rounded-none',
            'arch' => 'rounded-t-[999px] rounded-b-brand',
            'soft' => 'rounded-[calc(var(--brand-radius)*2)]',
            default => 'rounded-brand',
        }.' '.$extra);
    }

    /** Heading alignment for section headers. */
    public function align(): string
    {
        return $this->tokens['align'];
    }

    /** Small label above section headings in the template's eyebrow style. */
    public function eyebrow(?string $text, ?int $number = null, string $extra = ''): HtmlString
    {
        if (blank($text) || $this->tokens['eyebrow'] === 'none') {
            return new HtmlString('');
        }

        $text = e($text);
        $num = str_pad((string) ($number ?? 1), 2, '0', STR_PAD_LEFT);

        $html = match ($this->tokens['eyebrow']) {
            'pill' => '<span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3.5 py-1 text-xs font-semibold text-primary ring-1 ring-primary/20">'.$text.'</span>',
            'line' => '<span class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.22em] text-primary uppercase"><span class="h-px w-10 bg-primary"></span>'.$text.'</span>',
            'number' => '<span class="inline-flex items-baseline gap-3 text-xs font-semibold tracking-[0.18em] text-muted uppercase"><span class="font-mono text-primary">'.$num.'</span><span class="h-px w-6 translate-y-[-3px] bg-line"></span>'.$text.'</span>',
            'mono' => '<span class="font-mono text-xs font-medium tracking-wide text-primary">// '.strtolower($text).'</span>',
            'dot' => '<span class="inline-flex items-center gap-2 text-sm font-medium text-muted"><span class="size-2 rounded-full bg-primary"></span>'.$text.'</span>',
            'bracket' => '<span class="font-mono text-xs tracking-[0.2em] text-muted uppercase">[ <span class="text-primary">'.$text.'</span> ]</span>',
            default => '<span class="text-xs font-bold tracking-[0.2em] text-primary uppercase">'.$text.'</span>',
        };

        return new HtmlString($extra ? '<span class="'.e($extra).'">'.$html.'</span>' : $html);
    }

    /**
     * Attributes for a scroll-reveal element: {!! $ds->reveal(2) !!}
     * The index staggers sibling elements.
     */
    public function reveal(int $index = 0, ?string $type = null): HtmlString
    {
        if ($this->tokens['animation'] === 'none') {
            return new HtmlString('');
        }

        $type = $type ? ' data-reveal-type="'.e($type).'"' : '';

        return new HtmlString(' data-reveal'.$type.' style="--i:'.min($index, 12).'"');
    }

    /** Muted text class. */
    public function muted(): string
    {
        return 'text-muted';
    }

    /** Summary used by admin & gallery ("Light · Serif · Cards ..."). */
    public function summary(): array
    {
        return [
            'Theme' => ucfirst($this->tokens['theme']),
            'Typography' => ucwords(str_replace('-', ' ', $this->tokens['heading'])),
            'Navigation' => ComponentRegistry::label('navbar', $this->components['navbar']),
            'Hero' => ComponentRegistry::label('hero', $this->components['hero']),
            'Cards' => ucfirst($this->tokens['card']),
            'Footer' => ComponentRegistry::label('footer', $this->components['footer']),
            'Animation' => ucfirst($this->tokens['animation']),
        ];
    }
}
