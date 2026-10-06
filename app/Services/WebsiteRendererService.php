<?php

namespace App\Services;

use App\Models\CompanyPage;
use App\Models\CompanyProfile;
use App\Models\CompanySection;
use App\Models\Menu;
use App\Support\Website\Brand;
use App\Support\Website\DesignSystem;
use App\Support\Website\MenuLink;
use App\Support\Website\SiteContext;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Template + company data + pages + menus + sections → rendered website.
 *
 * Nothing is generated as static HTML: every request renders the Blade theme
 * of the company's template with its current data, so edits are visible
 * immediately.
 */
class WebsiteRendererService
{
    public function __construct(
        private readonly MenuService $menus,
        private readonly SeoService $seo,
    ) {}

    /**
     * Render the home page or a custom page (by slug) of a company website.
     */
    public function render(CompanyProfile $company, SiteContext $site, ?string $pageSlug = null): View
    {
        $this->loadRelations($company);

        $page = null;
        if ($pageSlug !== null) {
            $page = $this->visiblePages($company, $site)->firstWhere('slug', $pageSlug);
            abort_if($page === null, 404);
        }

        $layout = $company->layout();
        $view = $page ? "websites.templates.{$layout}.page" : "websites.templates.{$layout}.home";

        return view($view, $this->viewData($company, $site, $page));
    }

    public function viewData(CompanyProfile $company, SiteContext $site, ?CompanyPage $page = null): array
    {
        $brand = $company->brand();
        $layout = $company->layout();
        $ds = $layout === 'composer' ? $company->template->designSystem() : null;
        $sections = $this->sections($company);

        return [
            'company' => $company,
            'site' => $site,
            'page' => $page,
            'layout' => $layout,
            'ds' => $ds,
            'heroFirst' => ! $page && $sections->first()?->key === 'hero',
            'brand' => $brand,
            'brandCss' => Brand::cssVariables($brand),
            'fontsUrl' => Brand::fontsUrl($brand, $ds ? ['JetBrains Mono'] : []),
            'sections' => $sections,
            'menu' => $this->menu($company, $site),
            'pages' => $this->visiblePages($company, $site),
            'seo' => $this->seo->meta($company, $site, $page),
        ];
    }

    private function loadRelations(CompanyProfile $company): void
    {
        if (! $company->exists) {
            return; // Unsaved sample company (template preview): relations are set in memory.
        }

        $company->loadMissing([
            'template', 'sections', 'services', 'products', 'projects', 'team', 'testimonials',
            'gallery', 'pages', 'menus', 'primaryDomain',
        ]);
    }

    /**
     * Enabled sections in order, skipping sections that have no content yet.
     *
     * @return Collection<int, CompanySection>
     */
    public function sections(CompanyProfile $company): Collection
    {
        $known = array_keys(config('website-templates.sections'));
        $layout = $company->layout();

        return $company->sections
            ->filter(fn (CompanySection $section) => $section->is_enabled && in_array($section->key, $known, true))
            ->filter(fn (CompanySection $section) => $this->layoutSupports($layout, $section->key))
            ->sortBy('sort_order')
            ->filter(fn (CompanySection $section) => $this->hasContent($company, $section->key))
            ->values();
    }

    /**
     * Hand-crafted layouts only render the sections they ship views for
     * (e.g. "stats" and "clients" exist in composed templates only).
     */
    public function layoutSupports(string $layout, string $key): bool
    {
        return $layout === 'composer'
            || view()->exists("websites.templates.{$layout}.sections.{$key}")
            || view()->exists("websites.templates.corporate.sections.{$key}");
    }

    public function hasContent(CompanyProfile $company, string $key): bool
    {
        return match ($key) {
            'stats' => $company->stats() !== [],
            'clients' => count($company->clients()) >= 3,
            'about' => filled($company->about) || filled($company->description) || filled($company->vision) || filled($company->mission),
            'services', 'products', 'projects', 'team', 'testimonials', 'gallery' => $company->{$key}->isNotEmpty(),
            default => true,
        };
    }

    private function visiblePages(CompanyProfile $company, SiteContext $site): Collection
    {
        // Owners may preview draft pages; visitors only see published pages.
        return $company->pages
            ->filter(fn (CompanyPage $page) => $site->isPreview() || $page->isPublished())
            ->values();
    }

    /**
     * @return Collection<int, MenuLink>
     */
    public function menu(CompanyProfile $company, SiteContext $site): Collection
    {
        $pages = $this->visiblePages($company, $site)->keyBy('id');
        $tree = $this->menus->tree($company->menus, activeOnly: true);

        $toLink = function (Menu $menu) use ($site, $pages, &$toLink): ?MenuLink {
            $children = $menu->children->where('status', 'active')->map($toLink)->filter()->values();
            $active = false;

            switch ($menu->type) {
                case Menu::TYPE_PAGE:
                    $page = $pages->get($menu->company_page_id) ?? $pages->firstWhere('slug', $menu->url);
                    if (! $page) {
                        return $children->isEmpty() ? null : new MenuLink($menu->title, null, false, $children);
                    }
                    $url = $site->page($page->slug);
                    $active = $site->isCurrentPage($page->slug);
                    break;
                case Menu::TYPE_ANCHOR:
                    $url = $site->anchor($menu->url ?: 'hero');
                    break;
                case Menu::TYPE_URL:
                    $url = $menu->url ?: '#';
                    break;
                default:
                    if ($children->isEmpty()) {
                        return null;
                    }
                    $url = null;
            }

            return new MenuLink($menu->title, $url, (bool) $menu->open_in_new_tab, $children, $active || $children->contains('active', true));
        };

        $links = $tree->map($toLink)->filter()->values();

        return $company->hasShop() && $site->isLive() ? $this->withShopLinks($company, $site, $links) : $links;
    }

    /**
     * Shop ON: add "Shop" (with category sub menu) and "Keranjang" to the
     * navigation, right before the contact link.
     */
    private function withShopLinks(CompanyProfile $company, SiteContext $site, Collection $links): Collection
    {
        $categories = $company->productCategories()->where('status', 'active')->whereNull('parent_id')->take(8)->get();
        $children = $categories->map(fn ($c) => new MenuLink($c->name, $site->shop('category/'.$c->slug)));
        if ($children->isNotEmpty()) {
            $children->prepend(new MenuLink('Semua Produk', $site->shop('products')));
        }

        $shopLinks = collect([
            new MenuLink('Shop', $site->shop(), false, $children, request()->is('shop*')),
            new MenuLink('Keranjang', $site->shop('cart'), false, collect(), request()->is('shop/cart')),
        ]);

        $contactIndex = $links->search(fn (MenuLink $link) => str_ends_with((string) $link->url, '#contact'));
        $position = $contactIndex === false ? $links->count() : $contactIndex;

        return $links->slice(0, $position)->concat($shopLinks)->concat($links->slice($position))->values();
    }

    /**
     * View data for shop & account pages: the company's own template chrome
     * (navbar, footer, fonts, colors) plus a design system for shop content.
     */
    public function shopViewData(CompanyProfile $company, SiteContext $site, array $seo = []): array
    {
        $this->loadRelations($company);
        $data = $this->viewData($company, $site);
        $layout = $data['layout'];

        $data['ds'] ??= DesignSystem::forCraftedLayout($layout);
        $data['shopLayout'] = "websites.templates.{$layout}.layout";
        $data['isCrafted'] = $layout !== 'composer';
        $data['heroFirst'] = false;
        $data['seo'] = array_merge($data['seo'], ['og_type' => 'website'], $seo);
        $data['fontsUrl'] = Brand::fontsUrl($data['brand'], ['JetBrains Mono']);

        return $data;
    }
}
