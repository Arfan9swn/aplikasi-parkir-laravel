<?php

namespace App\Http\Controllers;

use App\Models\parkir_logs;
use App\Models\parkir_users;
use App\Support\SortHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Admin-panel user management: admins may list accounts and change a user's
 * role, username / name, or password. Owners additionally retain the full
 * parking panel; this controller itself is admin+owner only.
 */
class UserController extends Controller
{
    private const SORTABLE = ['username', 'nama_lengkap', 'role'];

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $q    = trim((string) $request->query('q', ''));
        $sort = SortHelper::field((string) $request->query('sort'), self::SORTABLE);
        $dir  = SortHelper::direction((string) $request->query('dir'));
        $me   = (int) (session('auth_user.id_user') ?? 0);

        $users = parkir_users::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(fn ($w) => $w
                    ->where('username', 'like', "%{$q}%")
                    ->orWhere('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%"));
            })
            ->orderBy($sort, $dir)
            ->get();

        return view('users.index', [
            'users'   => $users,
            'q'       => $q,
            'sortKey' => $sort . ' ' . $dir,
            'me'      => $me,
        ]);
    }

    /**
     * Change a user's role. Admins may assign petugas/admin; only an owner
     * can grant the owner role, and nobody can change their own role.
     */
    public function updateRole(Request $request, string $id)
    {
        $actor  = $this->authorizeAdmin();
        $target = parkir_users::find($id);

        if (! $target) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ((int) $target->id_user === (int) $actor['id_user']) {
            return redirect()->route('pengguna.index')->with('error', 'Anda tidak dapat mengubah role akun sendiri.');
        }

        $assignable = $actor['role'] === 'owner'
            ? ['petugas', 'admin', 'owner']
            : ['petugas', 'admin'];

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:' . implode(',', $assignable),
        ], [
            'role.required' => 'Role wajib dipilih.',
            'role.in'       => 'Role tidak valid untuk akun Anda.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pengguna.index')->withErrors($validator);
        }

        $old = $target->role;
        $target->update(['role' => $request->role]);

        $this->log($request, 'Mengubah role ' . $target->username . ' dari ' . $old . ' menjadi ' . $request->role);

        return redirect()->route('pengguna.index')->with('success', 'Role ' . $target->username . ' diubah menjadi ' . $request->role . '.');
    }
    /**
     * Change a user's username and/or display name.
     */
    public function updateProfile(Request $request, string $id)
    {
        $this->authorizeAdmin();
        $target = parkir_users::find($id);

        if (! $target) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        $validator = Validator::make($request->all(), [
            'username'     => 'required|string|min:3|max:255|unique:tb_user,username,' . $target->id_user . ',id_user',
            'nama_lengkap' => 'required|string|max:255',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min'      => 'Username minimal 3 karakter.',
            'username.unique'   => 'Username sudah digunakan.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pengguna.index')->withErrors($validator);
        }

        $old = $target->username;
        $target->update([
            'username'     => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        $this->log($request, 'Mengubah profil akun ' . $old . ' menjadi ' . $request->username);

        return redirect()->route('pengguna.index')->with('success', 'Profil akun diperbarui.');
    }

    /**
     * Set a new password for a user (hashed the same way as login).
     */
    public function updatePassword(Request $request, string $id)
    {
        $this->authorizeAdmin();
        $target = parkir_users::find($id);

        if (! $target) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('pengguna.index')->withErrors($validator);
        }

        $target->update(['password' => Hash::make($request->password)]);

        $this->log($request, 'Mengatur ulang password akun ' . $target->username);

        return redirect()->route('pengguna.index')->with('success', 'Password ' . $target->username . ' diperbarui.');
    }

    private function authorizeAdmin(): array
    {
        $user = session('auth_user');

        if (! $user || ! in_array($user['role'] ?? '', ['admin', 'owner'], true)) {
            abort(403, 'Akses dibatasi untuk admin.');
        }

        return $user;
    }

    private function log(Request $request, string $activity): void
    {
        $user = $request->session()->get('auth_user');

        if (! $user) {
            return;
        }

        try {
            parkir_logs::create([
                'id_user'         => $user['id_user'],
                'aktivitas'       => $activity,
                'waktu_aktivitas' => now()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // logging must never block the flow
        }
    }
}