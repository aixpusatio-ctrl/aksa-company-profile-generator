<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Services\Shop\ShopService;

/**
 * Base for seller dashboard shop controllers. Every action authorizes the
 * company (owner or admin) — company_profile_id is the tenant boundary and
 * all child models are resolved through scoped route bindings.
 */
abstract class SellerController extends Controller
{
    protected function authorizeShop(CompanyProfile $company): void
    {
        $this->authorize('update', $company);
        app(ShopService::class)->settings($company);
    }
}
