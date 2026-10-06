<?php

namespace App\Http\Middleware;

use App\Services\DomainService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The SaaS application (landing, auth, dashboard, admin) is only served on
 * central hosts, never on tenant sub domains or custom domains.
 */
class EnsureCentralDomain
{
    public function __construct(private readonly DomainService $domains) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->domains->isCentralHost($request->getHost()), 404);

        return $next($request);
    }
}
