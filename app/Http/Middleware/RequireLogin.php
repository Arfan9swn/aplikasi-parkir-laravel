<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Web-side login gate: guests are redirected to the login page instead of
 * receiving a JSON 401 (that is EnsureRole's job on /api/*).
 *
 * Usage: ->middleware('auth.web')
 */
class RequireLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('auth_user')) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
