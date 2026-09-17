<?php

namespace App\Http\Controllers;

use App\Models\parkir_kendaraans;
use App\Models\parkir_logs;
use App\Models\parkir_transaksis;
use App\Models\parkir_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KendaraanController extends Controller
{
    /**
     * Server-rendered vehicle registry.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $all = parkir_kendaraans::with('user')->get();

        if ($q !== '') {
            $needle = strtoupper($q);
            $all = $all->filter(function ($v) use ($needle) {
                return str_contains(strtoupper((string) $v->plat_nomor), $needle);
            })->values();
        }

        $parked = parkir_transaksis::where('status', 'masuk')->get();

        return view('ticket.kendaraan', [
            'vehicles'  => $all,
            'parkedNow' => $parked->pluck('id_kendaraan')->mapWithKeys(function ($id) {
                return [$id => true];
            })->all(),
            'q'         => $q,
            'canManage' => $this->canManage(),
        ]);
    }

    public function create()
    {
        $this->authorizeManage();

        return view('ticket.kendaraan-form', [
            'vehicle' => null,
            'users'   => parkir_users::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validator = Validator::make($request->all(), [
            'plat_nomor'      => 'required|string|unique:tb_kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required|string',
            'warna'           => 'nullable|string',
            'pemilik'         => 'nullable|string',
            'id_user'         => 'required|integer|exists:tb_user,id_user',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.kendaraan.create')->withInput()->withErrors($validator);
        }

        $data = $request->all();
        $data['plat_nomor'] = strtoupper(preg_replace('/\s+/', ' ', trim($request->plat_nomor)));
        $data['warna']      = trim($request->warna ?? '') ?: '—';
        $data['pemilik']    = trim($request->pemilik ?? '') ?: 'Tanpa nama';

        $kendaraan = parkir_kendaraans::create($data);
        $this->log($request, 'Menambahkan kendaraan ' . $kendaraan->plat_nomor);

        return redirect()->route('ticket.kendaraan')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeManage();

        $kendaraan = parkir_kendaraans::find($id);

        if (! $kendaraan) {
            return redirect()->route('ticket.kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }

        return view('ticket.kendaraan-form', [
            'vehicle' => $kendaraan,
            'users'   => parkir_users::all(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeManage();

        $kendaraan = parkir_kendaraans::find($id);

        if (! $kendaraan) {
            return redirect()->route('ticket.kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }

        $validator = Validator::make($request->all(), [
            'plat_nomor'      => 'required|string|unique:tb_kendaraan,plat_nomor,' . $id . ',id_kendaraan',
            'jenis_kendaraan' => 'required|string',
            'warna'           => 'nullable|string',
            'pemilik'         => 'nullable|string',
            'id_user'         => 'required|integer|exists:tb_user,id_user',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.kendaraan.edit', $id)->withInput()->withErrors($validator);
        }

        $data = $request->all();
        $data['plat_nomor'] = strtoupper(preg_replace('/\s+/', ' ', trim($request->plat_nomor)));
        $data['warna']      = trim($request->warna ?? '') ?: '—';
        $data['pemilik']    = trim($request->pemilik ?? '') ?: 'Tanpa nama';

        $kendaraan->update($data);
        $this->log($request, 'Memperbarui kendaraan ' . $kendaraan->plat_nomor);

        return redirect()->route('ticket.kendaraan')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Request $request, string $id)
    {
        $this->authorizeManage();

        $kendaraan = parkir_kendaraans::find($id);

        if (! $kendaraan) {
            return redirect()->route('ticket.kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }

        $kendaraan->delete();
        $this->log($request, 'Menghapus kendaraan ' . $kendaraan->plat_nomor);

        return redirect()->route('ticket.kendaraan')->with('success', 'Kendaraan berhasil dihapus.');
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