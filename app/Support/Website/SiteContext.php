<?php

namespace App\Support\Website;

/**
 * URL builder handed to website templates as $site.
 *
 * Templates never hard-code URLs: the same Blade theme renders the live
 * tenant website, the owner's draft preview inside the dashboard and the
 * template gallery preview, each with different link targets.
 */
class SiteContext
{
    public const MODE_LIVE = 'live';

    public const MODE_PREVIEW = 'preview';

    public const MODE_TEMPLATE = 'template';

    public function __construct(
        public readonly string $mode,
        private readonly string $baseUrl,
        public readonly bool $onHome = true,
        public readonly ?string $currentPage = null,
    ) {}

    public static function live(string $baseUrl, bool $onHome = true, ?string $currentPage = null): self
    {
        return new self(self::MODE_LIVE, rtrim($baseUrl, '/'), $onHome, $currentPage);
    }

    public static function preview(string $previewUrl, bool $onHome = true, ?string $currentPage = null): self
    {
        return new self(self::MODE_PREVIEW, $previewUrl, $onHome, $currentPage);
    }

    public static function template(string $previewUrl, bool $onHome = true, ?string $currentPage = null): self
    {
        return new self(self::MODE_TEMPLATE, $previewUrl, $onHome, $currentPage);
    }

    public function isLive(): bool
    {
        return $this->mode === self::MODE_LIVE;
    }

    public function isPreview(): bool
    {
        return $this->mode !== self::MODE_LIVE;
    }

    public function isTemplatePreview(): bool
    {
        return $this->mode === self::MODE_TEMPLATE;
    }

    /** Home page URL. */
    public function home(): string
    {
        return $this->isLive() ? $this->baseUrl.'/' : $this->baseUrl;
    }

    /** URL of a custom page. */
    public function page(string $slug): string
    {
        return $this->isLive()
            ? $this->baseUrl.'/'.$slug
            : $this->baseUrl.(str_contains($this->baseUrl, '?') ? '&' : '?').'page='.urlencode($slug);
    }

    /** Link to a section of the home page, e.g. anchor('services'). */
    public function anchor(string $section): string
    {
        return ($this->onHome ? '' : $this->home()).'#'.$section;
    }

    /** Absolute URL for a path on the live website. */
    public function url(string $path = ''): string
    {
        return $this->isLive() ? $this->baseUrl.'/'.ltrim($path, '/') : $this->home();
    }

    /** URL of a shop page on the live website, e.g. shop('product/kemeja-linen'). */
    public function shop(string $path = ''): string
    {
        return $this->isLive() ? $this->baseUrl.'/shop'.($path !== '' ? '/'.ltrim($path, '/') : '') : '#';
    }

    /** URL of a customer account page. */
    public function account(string $path = ''): string
    {
        return $this->isLive() ? $this->baseUrl.'/account'.($path !== '' ? '/'.ltrim($path, '/') : '') : '#';
    }

    /** Contact form endpoint (forms are disabled while previewing). */
    public function contactAction(): string
    {
        return $this->isLive() ? $this->baseUrl.'/contact' : '#contact';
    }

    public function canSubmitForms(): bool
    {
        return $this->isLive();
    }

    public function isCurrentPage(string $slug): bool
    {
        return $this->currentPage === $slug;
    }
}
