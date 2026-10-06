<?php

namespace App\Http\Middleware;

use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Makes sure the resolved tenant website can be served to visitors:
 * it must be published and its owner must not be suspended.
 */
class ResolveTenant
{
    public function __construct(private readonly CurrentTenant $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $company = $this->tenant->get();

        if (! $company || ! $company->isPublished() || $company->user?->isSuspended()) {
            return response()->view('websites.unavailable', ['company' => $company], 404);
        }

        return $next($request);
    }
}
