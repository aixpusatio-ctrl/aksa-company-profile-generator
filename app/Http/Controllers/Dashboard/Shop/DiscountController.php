<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Product;
use App\Models\Shop\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Product discounts (sale price + schedule) and tax configuration.
 */
class DiscountController extends SellerController
{
    public function index(Request $request, CompanyProfile $company): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.discounts.index', [
            'company' => $company,
            'products' => $company->shopProducts()->with('images')->where('status', '!=', Product::STATUS_ARCHIVED)
                ->when($request->boolean('on_sale'), fn ($q) => $q->whereNotNull('sale_price'))
                ->orderByRaw('sale_price is null')->orderBy('name')->paginate(30)->withQueryString(),
            'taxes' => $company->taxes()->get(),
        ]);
    }

    public function update(Request $request, CompanyProfile $company, Product $shopProduct): RedirectResponse
    {
        $this->authorizeShop($company);

        $data = $request->validate([
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:'.(float) $shopProduct->price],
            'sale_starts_at' => ['nullable', 'date'],
            'sale_ends_at' => ['nullable', 'date', 'after_or_equal:sale_starts_at'],
        ], ['sale_price.lt' => 'Harga promo harus lebih kecil dari harga normal.']);

        $shopProduct->update($data + ['sale_price' => null, 'sale_starts_at' => null, 'sale_ends_at' => null]);

        return back()->with('success', $shopProduct->sale_price ? 'Diskon produk disimpan.' : 'Diskon produk dihapus.');
    }

    public function storeTax(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);
        $data = $this->validatedTax($request);
        if ($data['is_active']) {
            $company->taxes()->update(['is_active' => false]);
        }
        $company->taxes()->create($data);

        return back()->with('success', 'Pajak ditambahkan.');
    }

    public function updateTax(Request $request, CompanyProfile $company, Tax $tax): RedirectResponse
    {
        $this->authorizeShop($company);
        $data = $this->validatedTax($request);
        if ($data['is_active']) {
            $company->taxes()->whereKeyNot($tax->id)->update(['is_active' => false]);
        }
        $tax->update($data);

        return back()->with('success', 'Pajak diperbarui.');
    }

    public function destroyTax(CompanyProfile $company, Tax $tax): RedirectResponse
    {
        $this->authorizeShop($company);
        $tax->delete();

        return back()->with('success', 'Pajak dihapus.');
    }

    private function validatedTax(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'inclusive' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return ['name' => $data['name'], 'rate' => $data['rate'], 'inclusive' => $request->boolean('inclusive'), 'is_active' => $request->boolean('is_active')];
    }
}
