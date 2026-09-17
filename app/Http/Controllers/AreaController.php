<?php

namespace App\Http\Controllers;

use App\Models\parkir_areas;
use App\Models\parkir_logs;
use App\Models\parkir_transaksis;
use App\Models\parkir_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    /**
     * Server-rendered list of parking areas — one petugas per area.
     */
    public function index()
    {
        $areas = parkir_areas::with('petugas')->get();

        return view('area.index', [
            'areas'    => $areas,
            'canManage' => $this->canManage(),
        ]);
    }

    public function create()
    {
        $this->authorizeManage();

        return view('area.create', [
            'area'           => null,
            'petugasOptions' => $this->petugasOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|unique:tb_area_parkir,nama_area',
            'kapasitas' => 'required|integer|min:1',
            'terisi'    => 'required|integer|min:0',
            'id_user'   => 'required|integer|exists:tb_user,id_user',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.area.create')->withInput()->withErrors($validator);
        }

        $petugasError = $this->petugasError($request->id_user, null);

        if ($petugasError) {
            return redirect()->route('ticket.area.create')->withInput()->with('error', $petugasError);
        }

        $area = parkir_areas::create($request->all());
        $this->log($request, 'Menambahkan area parkir "' . $area->nama_area . '"');

        return redirect()->route('ticket.area')->with('success', 'Area parkir berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeManage();

        $area = parkir_areas::with('petugas')->find($id);

        if (! $area) {
            return redirect()->route('ticket.area')->with('error', 'Area parkir tidak ditemukan.');
        }

        return view('area.edit', [
            'area'           => $area,
            'petugasOptions' => $this->petugasOptions($area->id_area),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeManage();

        $area = parkir_areas::find($id);

        if (! $area) {
            return redirect()->route('ticket.area')->with('error', 'Area parkir tidak ditemukan.');
        }

        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|unique:tb_area_parkir,nama_area,' . $id . ',id_area',
            'kapasitas' => 'required|integer|min:1',
            'terisi'    => 'required|integer|min:0',
            'id_user'   => 'required|integer|exists:tb_user,id_user',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.area.edit', $id)->withInput()->withErrors($validator);
        }

        $petugasError = $this->petugasError($request->id_user, $id);

        if ($petugasError) {
            return redirect()->route('ticket.area.edit', $id)->withInput()->with('error', $petugasError);
        }

        $area->update($request->all());
        $this->log($request, 'Memperbarui area parkir "' . $area->nama_area . '"');

        return redirect()->route('ticket.area')->with('success', 'Area parkir berhasil diperbarui.');
    }

    public function destroy(Request $request, string $id)
    {
        $this->authorizeManage();

        $area = parkir_areas::find($id);

        if (! $area) {
            return redirect()->route('ticket.area')->with('error', 'Area parkir tidak ditemukan.');
        }

        if (parkir_transaksis::where('id_area', $area->id_area)->exists()) {
            return redirect()->route('ticket.area')
                ->with('error', 'Area tidak dapat dihapus karena masih memiliki riwayat transaksi.');
        }

        $area->delete();
        $this->log($request, 'Menghapus area parkir "' . $area->nama_area . '"');

        return redirect()->route('ticket.area')->with('success', 'Area parkir berhasil dihapus.');
    }
// ------------------------------------------------------------
    // helpers
    // ------------------------------------------------------------
    private function canManage(): bool
    {
        $role = session('auth_user.role') ?? '';
        return in_array($role, ['admin', 'petugas'], true);
    }

    private function authorizeManage()
    {
        if (! $this->canManage()) {
            abort(403, 'Akses dibatasi untuk admin / petugas.');
        }
    }

    /**
     * Active petugas not yet assigned to another area (unless editing this one).
     */
    private function petugasOptions($excludeAreaId = null)
    {
        $taken = parkir_areas::where('id_user', '>', 0)
            ->when($excludeAreaId, fn ($q) => $q->where('id_area', '!=', $excludeAreaId))
            ->get()
            ->pluck('id_user')
            ->map(fn ($id) => (int) $id)
            ->all();

        return parkir_users::where('role', 'petugas')
            ->where('status_aktif', 1)
            ->get()
            ->filter(function ($u) use ($taken, $excludeAreaId) {
                if ($excludeAreaId) {
                    $current = parkir_areas::find($excludeAreaId);
                    if ($current && (int) $current->id_user === (int) $u->id_user) {
                        return true; // always allow the area's current petugas
                    }
                }
                return ! in_array((int) $u->id_user, $taken, true);
            })
            ->values();
    }

    private function petugasError($idUser, $excludeAreaId)
    {
        $petugas = parkir_users::find($idUser);

        if (! $petugas || $petugas->role !== 'petugas') {
            return 'Petugas yang dipilih tidak valid — pilih akun dengan role petugas.';
        }

        if ((int) $petugas->status_aktif !== 1) {
            return 'Petugas yang dipilih sedang dinonaktifkan.';
        }

        $query = parkir_areas::where('id_user', $petugas->id_user);

        if ($excludeAreaId) {
            $query = $query->where('id_area', '!=', $excludeAreaId);
        }

        if ($query->exists()) {
            return 'Petugas tersebut sudah ditugaskan ke area lain. Setiap area memerlukan petugas yang berbeda.';
        }

        return null;
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