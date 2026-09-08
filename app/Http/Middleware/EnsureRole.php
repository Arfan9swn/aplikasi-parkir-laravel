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
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin atau petugas yang dapat mengelola area parkir.',
            ], 403);
        }

        return $next($request);
    }
}