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
            'id_user'           => $user->id_user,
            'nama'              => $user->nama_lengkap,
            'username'          => $user->username,
            'role'              => $user->role,
            'status_verifikasi' => $user->status_verifikasi,
        ]);

        $this->logActivity($user, 'Login ke sistem.');

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
     * Register a new staff (petugas) account and sign them in. The account
     * stays 'menunggu' until an admin approves it.
     * POST /api/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:tb_user,email',
            'username'     => 'required|string|min:3|max:255|unique:tb_user,username',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Self-registration always creates a petugas account that stays 'menunggu'
        // until an admin approves it; admin/owner roles are granted via /api/users.
        $user = parkir_users::create([
            'nama_lengkap'       => $request->nama_lengkap,
            'email'              => $request->email,
            'username'           => $request->username,
            'password'           => Hash::make($request->password),
            'role'               => 'petugas',
            'status_aktif'       => 1,
            'status_verifikasi' => 'menunggu',
        ]);

        $this->logActivity($user, 'Registrasi akun baru');
        $user->issueEmailOtp();

        // auto-login right after registering
        $request->session()->put(self::SESSION_KEY, [
            'id_user'           => $user->id_user,
            'nama'              => $user->nama_lengkap,
            'username'          => $user->username,
            'role'              => $user->role,
            'status_verifikasi' => $user->status_verifikasi,
        ]);

        $this->logActivity($user, 'Login ke sistem.');

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Kode verifikasi dikirim ke email Anda dan akun menunggu persetujuan admin.',
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

    /**
     * Server-side login form handler (no JavaScript).
     * POST /login
     */
    public function loginWeb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')->withInput()->withErrors($validator);
        }

        $user = parkir_users::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return redirect()->route('login')->withInput()->with('error', 'Username atau password salah.');
        }

        if ((int) $user->status_aktif !== 1) {
            return redirect()->route('login')->withInput()->with('error', 'Akun ini dinonaktifkan. Silakan hubungi admin.');
        }

        $request->session()->put(self::SESSION_KEY, [
            'id_user'           => $user->id_user,
            'nama'              => $user->nama_lengkap,
            'username'          => $user->username,
            'role'              => $user->role,
            'status_verifikasi' => $user->status_verifikasi,
        ]);

        $this->logActivity($user, 'Login ke sistem.');

        if (! $user->isVerified()) {
            return redirect()->route('verifikasi.menunggu');
        }

        return redirect()->route('beranda');
    }

    /**
     * Server-side registration form handler (no JavaScript).
     * POST /registrasi
     */
    public function registerWeb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:tb_user,email',
            'username'     => 'required|string|min:3|max:255|unique:tb_user,username',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah digunakan.',
            'username.required'     => 'Username wajib diisi.',
            'username.min'          => 'Username minimal 3 karakter.',
            'username.unique'       => 'Username sudah digunakan.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $user = parkir_users::create([
            'nama_lengkap'       => $request->nama_lengkap,
            'email'              => $request->email,
            'username'           => $request->username,
            'password'           => Hash::make($request->password),
            'role'               => 'petugas',
            'status_aktif'       => 1,
            'status_verifikasi' => 'menunggu',
        ]);

        $this->logActivity($user, 'Registrasi akun baru');
        $user->issueEmailOtp();

        $request->session()->put(self::SESSION_KEY, [
            'id_user'           => $user->id_user,
            'nama'              => $user->nama_lengkap,
            'username'          => $user->username,
            'role'              => $user->role,
            'status_verifikasi' => $user->status_verifikasi,
        ]);

        $this->logActivity($user, 'Login ke sistem.');

        return redirect()->route('verifikasi.menunggu')
            ->with('success', 'Pendaftaran berhasil. Kode verifikasi 6 digit sudah dikirim ke email Anda.');
    }

    /**
     * Waiting room for accounts an admin has not approved yet. Approved accounts
     * are forwarded to beranda, so reloading doubles as a status check.
     * GET /verifikasi
     */
    public function verifikasiMenunggu(Request $request)
    {
        $sessionUser = $request->session()->get(self::SESSION_KEY);

        $user = parkir_users::find($sessionUser['id_user'] ?? null);

        if (! $user) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('login');
        }

        $request->session()->put(self::SESSION_KEY . '.status_verifikasi', $user->status_verifikasi);

        if ($user->isVerified()) {
            return redirect()->route('beranda')
                ->with('success', 'Akun Anda sudah disetujui admin dan siap digunakan.');
        }

        return view('auth.verifikasi', ['user' => $user]);
    }

    /**
     * Check the 6-digit OTP against the stored hash. Success marks the email
     * verified; the admin gate is still required afterwards.
     * POST /verifikasi/otp
     */
    public function verifyEmailOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp.required' => 'Kode verifikasi wajib diisi.',
            'otp.regex'    => 'Kode verifikasi harus 6 angka.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = $this->currentUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->status_verifikasi === 'ditolak') {
            return redirect()->route('verifikasi.menunggu')
                ->with('error', 'Pendaftaran akun ini ditolak, kode verifikasi tidak berlaku lagi.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('verifikasi.menunggu')
                ->with('success', 'Email sudah terverifikasi. Tinggal menunggu persetujuan admin.');
        }

        if (! $user->otp_code || ! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Kode sudah kedaluwarsa. Minta kode baru lewat tombol Kirim Ulang.']);
        }

        if (! Hash::check($request->otp, $user->otp_code)) {
            return back()->withErrors(['otp' => 'Kode tidak valid. Periksa lagi email Anda.']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'otp_code'          => null,
            'otp_expires_at'    => null,
        ])->save();

        $this->logActivity($user, 'Memverifikasi email ' . $user->email);

        return redirect()->route('verifikasi.menunggu')
            ->with('success', 'Email terverifikasi. Tinggal menunggu persetujuan admin.');
    }

    /**
     * Rotate and resend the OTP; the previous code stops working.
     * POST /verifikasi/otp/kirim-ulang
     */
    public function resendEmailOtp(Request $request)
    {
        $user = $this->currentUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->status_verifikasi === 'ditolak') {
            return back()->with('error', 'Pendaftaran akun ini sudah ditolak admin.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email sudah terverifikasi, kode baru tidak diperlukan.');
        }

        $user->issueEmailOtp();

        return back()->with('success', 'Kode baru dikirim ke ' . $user->email . '. Kode sebelumnya tidak berlaku lagi.');
    }

    private function currentUser(Request $request): ?parkir_users
    {
        $sessionUser = $request->session()->get(self::SESSION_KEY);

        return parkir_users::find($sessionUser['id_user'] ?? null);
    }

    private function flushSession(Request $request): void
    {
        $session = $request->session()->get(self::SESSION_KEY);

        if ($session) {
            $user = parkir_users::find($session['id_user'] ?? null);
            if ($user) {
                $this->logActivity($user, 'Logout dari sistem.');
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