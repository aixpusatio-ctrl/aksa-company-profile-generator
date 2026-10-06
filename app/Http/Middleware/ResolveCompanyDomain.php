<?php

namespace App\Http\Middleware;

use App\Services\DomainService;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Request hostname → company profile.
 *
 *   1. custom domain (www.company.com / company.com)
 *   2. platform sub domain (company.platform.test)
 */
class ResolveCompanyDomain
{
    public function __construct(
        private readonly DomainService $domains,
        private readonly CurrentTenant $tenant,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // The host is captured by the route's domain pattern; controllers don't need it.
        $request->route()?->forgetParameter('tenant_host');

        $company = $this->domains->resolveHost($request->getHost());

        if (! $company) {
            return response()->view('websites.not-found', ['host' => $request->getHost()], 404);
        }

        $this->tenant->set($company);
        $request->attributes->set('company', $company);

        return $next($request);
    }
}
