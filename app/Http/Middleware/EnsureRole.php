<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate API endpoints behind the session user created by AuthController.
 *
 * Usage: ->middleware('role:admin,petugas')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->session()->get('auth_user');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        if (!empty($roles) && !in_array($user['role'], $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.',
                ], 403);
            }

            // Web pages get a friendly redirect instead of raw JSON.
            return redirect()
                ->route('beranda')
                ->with('error', 'Akses ditolak — halaman ini bukan bagian dari panel Anda.');
        }

        return $next($request);
    }
}