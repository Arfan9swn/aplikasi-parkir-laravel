<?php

namespace App\Http\Controllers;

use App\Models\parkir_logs;
use App\Models\parkir_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private const SESSION_KEY = 'auth_user';

    /**
     * Authenticate against tb_user and start a session.
     * POST /api/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = parkir_users::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.'
            ], 401);
        }

        if ((int) $user->status_aktif !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini dinonaktifkan. Silakan hubungi admin.'
            ], 403);
        }

        $request->session()->put(self::SESSION_KEY, [
            'id_user'  => $user->id_user,
            'nama'     => $user->nama_lengkap,
            'username' => $user->username,
            'role'     => $user->role,
        ]);

        $this->logActivity($user, 'Login ke sistem ParkEase');

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil. Selamat datang, ' . $user->nama_lengkap . '!',
            'data'    => $request->session()->get(self::SESSION_KEY)
        ], 200);
    }

    /**
     * Return the current session user (or null when logged out).
     * GET /api/me
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => $request->session()->get(self::SESSION_KEY)
        ], 200);
    }

    /**
     * Register a new staff (petugas) account and sign them in.
     * POST /api/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:3|max:255|unique:tb_user,username',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Self-registration always creates an active staff account —
        // admin/owner roles can only be granted by an admin via /api/users.
        $user = parkir_users::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'role'         => 'petugas',
            'status_aktif' => 1,
        ]);

        $this->logActivity($user, 'Registrasi akun baru');

        // auto-login right after registering
        $request->session()->put(self::SESSION_KEY, [
            'id_user'  => $user->id_user,
            'nama'     => $user->nama_lengkap,
            'username' => $user->username,
            'role'     => $user->role,
        ]);

        $this->logActivity($user, 'Login ke sistem ParkEase');

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Selamat datang, ' . $user->nama_lengkap . '!',
            'data'    => $request->session()->get(self::SESSION_KEY)
        ], 201);
    }

    /**
     * End the session (API).
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $this->flushSession($request);

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.'
        ], 200);
    }

    /**
     * End the session (web form in the navbar) and go back to the home page.
     * POST /logout
     */
    public function logoutWeb(Request $request)
    {
        $this->flushSession($request);

        return redirect()->route('beranda');
    }

    private function flushSession(Request $request): void
    {
        $session = $request->session()->get(self::SESSION_KEY);

        if ($session) {
            $user = parkir_users::find($session['id_user'] ?? null);
            if ($user) {
                $this->logActivity($user, 'Logout dari sistem ParkEase');
            }
        }

        $request->session()->forget(self::SESSION_KEY);
    }

    private function logActivity(parkir_users $user, string $activity): void
    {
        try {
            parkir_logs::create([
                'id_user'         => $user->id_user,
                'aktivitas'       => $activity,
                'waktu_aktivitas' => now()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // logging must never block authentication
        }
    }
}