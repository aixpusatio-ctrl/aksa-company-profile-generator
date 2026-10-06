<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShippingController extends SellerController
{
    public function index(CompanyProfile $company): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.shipping.index', ['company' => $company, 'methods' => $company->shippingMethods()->get()]);
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);
        $company->shippingMethods()->create($this->validated($request) + ['provider' => 'manual', 'sort_order' => $company->shippingMethods()->count() + 1]);

        return back()->with('success', 'Metode pengiriman ditambahkan.');
    }

    public function update(Request $request, CompanyProfile $company, ShippingMethod $shippingMethod): RedirectResponse
    {
        $this->authorizeShop($company);
        $shippingMethod->update($this->validated($request));

        return back()->with('success', 'Metode pengiriman diperbarui.');
    }

    public function destroy(CompanyProfile $company, ShippingMethod $shippingMethod): RedirectResponse
    {
        $this->authorizeShop($company);
        $shippingMethod->delete();

        return back()->with('success', 'Metode pengiriman dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(ShippingMethod::TYPES))],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'estimate' => ['nullable', 'string', 'max:60'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'per_kg' => ['nullable', 'numeric', 'min:0'],
            'cities' => ['nullable', 'string', 'max:3000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // "Jakarta=15000" per line → {"Jakarta": 15000}
        $cities = collect(preg_split('/\r\n|\r|\n/', (string) ($data['cities'] ?? '')))
            ->map(fn ($line) => array_map('trim', explode('=', $line, 2)))
            ->filter(fn ($pair) => count($pair) === 2 && $pair[0] !== '' && is_numeric($pair[1]))
            ->mapWithKeys(fn ($pair) => [$pair[0] => (float) $pair[1]])
            ->all();

        return [
            'type' => $data['type'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'estimate' => $data['estimate'] ?? null,
            'cost' => (float) ($data['cost'] ?? 0),
            'min_order' => $data['min_order'] ?? null,
            'config' => $data['type'] === 'custom' ? array_filter(['base' => (float) ($data['cost'] ?? 0), 'per_kg' => (float) ($data['per_kg'] ?? 0), 'cities' => $cities]) : null,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
