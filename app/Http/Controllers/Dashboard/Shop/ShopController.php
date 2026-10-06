<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\ShopSetting;
use App\Services\MediaService;
use App\Services\Shop\InventoryService;
use App\Services\Shop\ShopService;
use App\Support\Shop\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShopController extends SellerController
{
    public function __construct(private readonly ShopService $shop) {}

    /** Sidebar "Shop": pick a website (or go straight to the only one). */
    public function hub(Request $request): View|RedirectResponse
    {
        $websites = $request->user()->companyProfiles()->with('shopSetting')->orderBy('name')->get();

        if ($websites->count() === 1) {
            return redirect()->route('websites.shop.overview', $websites->first());
        }

        return view('dashboard.shop.hub', compact('websites'));
    }

    public function overview(CompanyProfile $company, InventoryService $inventory): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.overview', [
            'company' => $company,
            'stats' => $this->shop->stats($company),
            'recentOrders' => $company->orders()->take(6)->get(),
            'lowStock' => $inventory->lowStock($company)->take(6),
            'pendingReviews' => $company->productReviews()->where('status', 'pending')->count(),
        ]);
    }

    public function toggle(CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);

        $company->hasShop() ? $this->shop->disable($company) : $this->shop->enable($company);

        return back()->with('success', $company->hasShop() ? 'Online shop aktif! Menu Shop & Keranjang kini tampil di website.' : 'Online shop dinonaktifkan.');
    }

    public function settings(CompanyProfile $company): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.settings', [
            'company' => $company,
            'settings' => $company->shopSetting,
            'currencies' => array_keys(Money::CURRENCIES),
        ]);
    }

    public function updateSettings(Request $request, CompanyProfile $company, MediaService $media): RedirectResponse
    {
        $this->authorizeShop($company);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'currency' => ['required', Rule::in(array_keys(Money::CURRENCIES))],
            'order_prefix' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9]+$/'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:100000'],
            'options' => ['nullable', 'array'],
            'options.min_order' => ['nullable', 'numeric', 'min:0'],
            'options.products_per_page' => ['nullable', 'integer', 'min:4', 'max:48'],
            'options.shop_title' => ['nullable', 'string', 'max:120'],
            'options.shop_subtitle' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', MediaService::imageRule()],
            'banner_image_media' => ['nullable', 'string', 'max:255'],
            'banner_image_remove' => ['nullable', 'boolean'],
        ]);

        $settings = $company->shopSetting;
        $options = $settings->options ?? [];

        foreach (array_keys(ShopSetting::DEFAULT_OPTIONS) as $key) {
            if (is_bool(ShopSetting::DEFAULT_OPTIONS[$key])) {
                $options[$key] = $request->boolean('options.'.$key);
            } elseif (array_key_exists($key, $data['options'] ?? [])) {
                $options[$key] = $data['options'][$key];
            }
        }
        $options['banner_image'] = $media->resolveImageInput($request->all(), 'banner_image', $request->user(), $company, $options['banner_image'] ?? null);

        $settings->update([
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'currency' => $data['currency'],
            'order_prefix' => strtoupper($data['order_prefix']),
            'low_stock_threshold' => $data['low_stock_threshold'],
            'options' => $options,
        ]);

        return back()->with('success', 'Pengaturan toko disimpan.');
    }

    /** Shop homepage section builder (enable/disable/reorder). */
    public function updateSections(Request $request, CompanyProfile $company): JsonResponse|RedirectResponse
    {
        $this->authorizeShop($company);

        $data = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.key' => ['required', Rule::in(array_keys(ShopSetting::SECTIONS))],
            'sections.*.enabled' => ['required', 'boolean'],
        ]);

        $company->shopSetting->update(['sections' => collect($data['sections'])->map(fn ($s) => ['key' => $s['key'], 'enabled' => (bool) $s['enabled']])->values()->all()]);

        return $request->expectsJson() ? response()->json(['saved' => true]) : back()->with('success', 'Susunan section toko disimpan.');
    }
}
