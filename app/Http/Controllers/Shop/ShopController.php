<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Services\Shop\CartService;
use App\Services\Shop\CatalogService;
use App\Services\Shop\ShopService;
use App\Services\Shop\WishlistService;
use App\Services\WebsiteRendererService;
use App\Support\CurrentTenant;
use App\Support\Website\SiteContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Base for storefront & customer account controllers: renders shop views
 * inside the company's own template (navbar, footer, branding).
 */
abstract class ShopController extends Controller
{
    protected function company(): CompanyProfile
    {
        return app(CurrentTenant::class)->get() ?? abort(404);
    }

    protected function site(Request $request): SiteContext
    {
        return SiteContext::live($request->getSchemeAndHttpHost(), false);
    }

    protected function shopView(Request $request, string $view, array $data = [], array $seo = []): View
    {
        $company = $this->company();
        $settings = app(ShopService::class)->settings($company);

        $shared = app(WebsiteRendererService::class)->shopViewData($company, $this->site($request), $seo + [
            'title' => ($seo['title'] ?? $settings->displayName()).' | '.$company->name,
        ]);

        return view($view, array_merge($shared, [
            'shop' => $settings,
            'categoriesTree' => app(CatalogService::class)->categoryTree($company),
            'cartCount' => app(CartService::class)->count($company),
            'wishlistIds' => app(WishlistService::class)->ids($company),
            'customer' => app(CartService::class)->customer($company),
        ], $data));
    }

    /** JSON for AJAX requests, redirect back (with flash) otherwise. */
    protected function respond(Request $request, array $payload, string $message)
    {
        return $request->expectsJson()
            ? response()->json($payload + ['message' => $message])
            : back()->with('shop_toast', $message);
    }
}
