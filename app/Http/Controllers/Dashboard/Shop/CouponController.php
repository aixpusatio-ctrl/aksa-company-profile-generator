<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends SellerController
{
    public function index(CompanyProfile $company): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.coupons.index', [
            'company' => $company,
            'coupons' => $company->coupons()->withSum('usages', 'discount')->get(),
        ]);
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorizeShop($company);
        $company->coupons()->create($this->validated($request, $company));

        return back()->with('success', 'Kupon dibuat.');
    }

    public function update(Request $request, CompanyProfile $company, Coupon $coupon): RedirectResponse
    {
        $this->authorizeShop($company);
        $coupon->update($this->validated($request, $company, $coupon));

        return back()->with('success', 'Kupon diperbarui.');
    }

    public function destroy(CompanyProfile $company, Coupon $coupon): RedirectResponse
    {
        $this->authorizeShop($company);
        $coupon->delete();

        return back()->with('success', 'Kupon dihapus.');
    }

    private function validated(Request $request, CompanyProfile $company, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => Str::upper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('coupons')->where('company_profile_id', $company->id)->ignore($coupon?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Coupon::TYPES))],
            'value' => ['required_unless:type,free_shipping', 'nullable', 'numeric', 'min:0', Rule::when($request->input('type') === 'percentage', ['max:100'])],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_customer' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // Free shipping coupons have no value (column is NOT NULL).
        $data['value'] ??= 0;

        return $data;
    }
}
