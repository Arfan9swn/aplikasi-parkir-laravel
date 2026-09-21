<?php

namespace App\Http\Controllers;

use App\Models\parkir_areas;
use App\Models\parkir_kendaraans;
use App\Models\parkir_logs;
use App\Models\parkir_transaksis;
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
            'users'     => $users,
            'q'         => $q,
            'sortKey'   => $sort . ' ' . $dir,
            'me'        => $me,
            'actorRole' => session('auth_user.role'),
        ]);
    }

    /**
     * Change a user's role between petugas and admin.
     * The owner role is NOT assignable here — ownership moves only through
     * transferOwnership() so the system always has exactly one owner.
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

        if ($target->role === 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Gunakan Transfer Owner untuk mengubah status kepemilikan.');
        }

        if ($target->role === 'admin' && $actor['role'] !== 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Akun admin lain tidak dapat diubah.');
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:petugas,admin',
        ], [
            'role.required' => 'Role wajib dipilih.',
            'role.in'       => 'Role hanya boleh petugas atau admin.',
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
     * Move ownership to another account. The previous owner (all of them, in
     * case of a data anomaly) is demoted to admin, guaranteeing that exactly
     * one owner exists at any time. Available to admins and the owner.
     */
    public function transferOwnership(Request $request)
    {
        $this->authorizeAdmin();

        $target = parkir_users::find($request->input('target_id'));

        if (! $target) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($target->role === 'owner') {
            return redirect()->route('pengguna.index')->with('error', $target->username . ' sudah menjadi owner.');
        }

        $previous = parkir_users::where('role', 'owner')->get();
        $previousNames = $previous->pluck('username')->implode(', ');

        parkir_users::where('role', 'owner')->update(['role' => 'admin']);
        $target->update(['role' => 'owner']);

        $this->log($request, 'Memindahkan kepemilikan dari ' . $previousNames . ' ke ' . $target->username);

        return redirect()->route('pengguna.index')->with('success', 'Kepemilikan dipindahkan ke ' . $target->username . '. Owner sebelumnya menjadi admin.');
    }

    /**
     * Remove a worker account. Workers with related history (logs, tickets,
     * vehicles, assigned areas) are deactivated instead of deleted so records
     * keep pointing at a real account. Admin and owner accounts can never be
     * removed through this endpoint.
     */
    public function destroy(Request $request, string $id)
    {
        $actor  = $this->authorizeAdmin();
        $target = parkir_users::find($id);

        if (! $target) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($target->role === 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Owner tidak dapat dihapus. Transfer kepemilikan terlebih dahulu.');
        }

        if ($target->role === 'admin' && $actor['role'] !== 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Akun admin lain tidak dapat dihapus.');
        }

        if ((int) $target->id_user === (int) $actor['id_user']) {
            return redirect()->route('pengguna.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $hasHistory = parkir_logs::where('id_user', $target->id_user)->exists()
            || parkir_transaksis::where('id_user', $target->id_user)->exists()
            || parkir_kendaraans::where('id_user', $target->id_user)->exists()
            || parkir_areas::where('id_user', $target->id_user)->exists();

        if ($hasHistory) {
            $target->update(['status_aktif' => 0]);
            $this->log($request, 'Menonaktifkan akun ' . $target->username . ' (masih memiliki data terkait).');

            return redirect()->route('pengguna.index')->with('success', $target->username . ' dinonaktifkan karena masih memiliki data terkait.');
        }

        $name = $target->username;
        $target->delete();

        $this->log($request, 'Menghapus akun ' . $name);

        return redirect()->route('pengguna.index')->with('success', 'Akun ' . $name . ' dihapus.');
    }

    /**
     * Change a user's username and/or display name.
     */
    public function updateProfile(Request $request, string $id)
    {
        $actor  = $this->authorizeAdmin();
        $target = parkir_users::find($id);

        if (! $target) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($target->role === 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Gunakan Transfer Owner untuk mengubah status kepemilikan.');
        }

        if ($target->role === 'admin' && $actor['role'] !== 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Akun admin lain tidak dapat diubah.');
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
        $actor  = $this->authorizeAdmin();
        $target = parkir_users::find($id);

        if (! $target) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($target->role === 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Gunakan Transfer Owner untuk mengubah status kepemilikan.');
        }

        if ($target->role === 'admin' && $actor['role'] !== 'owner') {
            return redirect()->route('pengguna.index')->with('error', 'Akun admin lain tidak dapat diubah.');
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