<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends SellerController
{
    public function index(CompanyProfile $company): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.payments.index', ['company' => $company, 'methods' => $company->paymentMethods()->get()]);
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);
        $company->paymentMethods()->create($this->validated($request) + ['provider' => 'manual', 'sort_order' => $company->paymentMethods()->count() + 1]);

        return back()->with('success', 'Metode pembayaran ditambahkan.');
    }

    public function update(Request $request, CompanyProfile $company, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->authorizeShop($company);
        $paymentMethod->update($this->validated($request));

        return back()->with('success', 'Metode pembayaran diperbarui.');
    }

    public function destroy(CompanyProfile $company, PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->authorizeShop($company);
        $paymentMethod->delete();

        return back()->with('success', 'Metode pembayaran dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(PaymentMethod::TYPES))],
            'name' => ['required', 'string', 'max:100'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'bank' => ['nullable', 'string', 'max:60'],
            'account_number' => ['nullable', 'string', 'max:40', 'regex:/^[0-9\-\s]+$/'],
            'account_name' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'type' => $data['type'],
            'name' => $data['name'],
            'instructions' => $data['instructions'] ?? null,
            'config' => $data['type'] === 'bank_transfer' ? array_filter(['bank' => $data['bank'] ?? null, 'account_number' => $data['account_number'] ?? null, 'account_name' => $data['account_name'] ?? null]) : null,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
