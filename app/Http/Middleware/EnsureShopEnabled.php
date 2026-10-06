<?php

namespace App\Http\Middleware;

use App\Services\Shop\ShopService;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Shop & customer-account routes of a tenant website: the shop must be
 * enabled, and a logged-in customer must belong to THIS shop (tenant
 * boundary for the "customer" guard).
 */
class EnsureShopEnabled
{
    public function __construct(
        private readonly CurrentTenant $tenant,
        private readonly ShopService $shop,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $company = $this->tenant->get();

        abort_unless($company && $company->hasShop(), 404);

        $customer = Auth::guard('customer')->user();
        if ($customer && $customer->company_profile_id !== $company->id) {
            Auth::guard('customer')->logout();
        }

        $this->shop->settings($company);

        return $next($request);
    }
}
