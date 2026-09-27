<?php

namespace App\Http\Middleware;

use App\Models\parkir_users;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keep accounts an admin has not approved away from the staff routes. The
 * status is re-read from the database on every request, so an approval takes
 * effect without the user logging in again.
 *
 * Usage: ->middleware('verified.web')
 */
class EnsureVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionUser = $request->session()->get('auth_user');

        if (! $sessionUser) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        }

        $user = parkir_users::find($sessionUser['id_user'] ?? null);

        if (! $user) {
            $request->session()->forget('auth_user');

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda tidak ditemukan. Silakan hubungi admin.',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda tidak ditemukan. Silakan hubungi admin.');
        }

        // Views read this copy to decide which navigation links to show.
        $request->session()->put('auth_user.status_verifikasi', $user->status_verifikasi);

        if ($user->isVerified()) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $user->status_verifikasi === 'menunggu'
                    ? 'Akun Anda menunggu persetujuan admin.'
                    : 'Pendaftaran akun Anda ditolak admin.',
            ], 403);
        }

        return redirect()->route('verifikasi.menunggu');
    }
}