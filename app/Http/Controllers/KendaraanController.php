<?php

namespace App\Http\Controllers;

use App\Models\parkir_kendaraans;
use App\Models\parkir_logs;
use App\Models\parkir_tarifs;
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

        return view('kendaraan.index', [
            'vehicles'  => $all,
            'parkedNow' => $parked->pluck('id_kendaraan')->mapWithKeys(function ($id) {
                return [$id => true];
            })->all(),
            'q'         => $q,
            'canManage' => $this->canManage(),
        ]);
    }

    /**
     * One vehicle's parking history, grouped by the area it was parked in.
     */
    public function show(string $id)
    {
        $kendaraan = parkir_kendaraans::with('user')->find($id);

        if (! $kendaraan) {
            return redirect()->route('ticket.kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }

        $riwayat = parkir_transaksis::with(['area', 'tarif'])
            ->where('id_kendaraan', $kendaraan->id_kendaraan)
            ->get()
            ->sortByDesc('waktu_masuk')
            ->values();

        $visits = $riwayat->map(function ($t) {
            $selesai = $t->status === 'keluar' && $t->waktu_keluar;

            return [
                'ticket'  => $t,
                'ongoing' => ! $selesai,
                // Settled tickets carry their billed hours; an active one is
                // still running, so bill it the same way check-out would.
                'durasi'  => $selesai ? (int) ($t->durasi_jam ?? 0) : $this->durasiJam($t->waktu_masuk),
                'biaya'   => (float) ($t->biaya_total ?? 0),
            ];
        });

        $perArea = [];

        foreach ($visits as $visit) {
            $nama = $visit['ticket']->area->nama_area ?? 'Tanpa area';

            if (! isset($perArea[$nama])) {
                $perArea[$nama] = [
                    'nama_area'     => $nama,
                    'kunjungan'     => 0,
                    'total_jam'     => 0,
                    'total_biaya'   => 0.0,
                    'sedang_parkir' => false,
                    'visits'        => [],
                ];
            }

            $perArea[$nama]['kunjungan']++;
            $perArea[$nama]['total_jam'] += $visit['durasi'];
            $perArea[$nama]['total_biaya'] += $visit['biaya'];
            $perArea[$nama]['sedang_parkir'] = $perArea[$nama]['sedang_parkir'] || $visit['ongoing'];
            $perArea[$nama]['visits'][] = $visit;
        }

        // Busiest area first — that is where this vehicle usually goes.
        usort($perArea, fn ($a, $b) => $b['kunjungan'] <=> $a['kunjungan']);

        $kunjungan = $visits->count();
        $totalJam  = (int) $visits->sum('durasi');

        return view('kendaraan.show', [
            'vehicle'      => $kendaraan,
            'perArea'      => $perArea,
            'kunjungan'    => $kunjungan,
            'totalJam'     => $totalJam,
            'totalBiaya'   => (float) $visits->sum('biaya'),
            'sedangParkir' => $visits->contains('ongoing', true),
            'rataRata'     => $kunjungan > 0 ? round($totalJam / $kunjungan, 1) : 0,
            'canManage'    => $this->canManage(),
        ]);
    }

    public function create()
    {
        $this->authorizeManage();

        return view('kendaraan.create', [
            'vehicle' => null,
            'users'   => parkir_users::all(),
            'tarifs'  => parkir_tarifs::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validator = Validator::make($request->all(), [
            'plat_nomor'      => 'required|string|unique:tb_kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required|string|in:' . implode(',', parkir_tarifs::pluck('jenis_kendaraan')->all()),
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

        return view('kendaraan.edit', [
            'vehicle' => $kendaraan,
            'users'   => parkir_users::all(),
            'tarifs'  => parkir_tarifs::all(),
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
            'jenis_kendaraan' => 'required|string|in:' . implode(',', parkir_tarifs::pluck('jenis_kendaraan')->all()),
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
    /**
     * Hours a vehicle has been (or was) parked, rounded up, minimum 1.
     * Mirrors the check-out billing so the history matches the receipt.
     */
    private function durasiJam($waktuMasuk): int
    {
        if (! $waktuMasuk) {
            return 0;
        }

        $menit = (int) ceil(abs(\Carbon\Carbon::parse($waktuMasuk)->diffInSeconds(\Carbon\Carbon::now(), false)) / 60);

        return max(1, (int) ceil($menit / 60));
    }

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