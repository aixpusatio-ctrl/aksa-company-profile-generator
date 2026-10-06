<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer account pages require a logged-in shop customer.
 */
class AuthenticateCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('customer')->check()) {
            session()->put('url.intended', $request->fullUrl());

            return redirect()->to($request->getSchemeAndHttpHost().'/account/login');
        }

        return $next($request);
    }
}
