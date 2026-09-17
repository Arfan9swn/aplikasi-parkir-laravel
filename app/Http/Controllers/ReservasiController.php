<?php

namespace App\Http\Controllers;

use App\Models\parkir_areas;
use App\Models\parkir_logs;
use App\Models\parkir_reservasis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Guest-facing reservation flow: any visitor may request a spot in an area;
 * staff confirm or cancel the request from the same page.
 */
class ReservasiController extends Controller
{
    /**
     * Reservation form (guest) — preselects an area when ?area= is given.
     */
    public function create(Request $request)
    {
        return view('reservasi.create', [
            'areas'       => parkir_areas::with('petugas')->orderBy('nama_area')->get(),
            'selectedId'  => (string) $request->query('area', ''),
            'reservasiId' => null,
        ]);
    }

    /**
     * Store a guest reservation request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_area'       => 'required|integer|exists:tb_area_parkir,id_area',
            'plat_nomor'    => 'required|string|max:20',
            'pemilik'       => 'nullable|string|max:100',
            'kontak'        => 'nullable|string|max:100',
            'waktu_datang'  => 'required|date|after:now',
        ], [
            'waktu_datang.after' => 'Waktu datang harus di masa depan.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('reservasi.create')->withInput()->withErrors($validator);
        }

        $area = parkir_areas::find($request->id_area);

        if (! $area) {
            return redirect()->route('reservasi.create')->withInput()
                ->with('error', 'Area parkir tidak ditemukan.');
        }

        if ((int) $area->terisi >= (int) $area->kapasitas) {
            return redirect()->route('reservasi.create')->withInput()
                ->with('error', 'Area ' . $area->nama_area . ' sedang penuh. Silakan pilih area lain.');
        }

        $reservasi = parkir_reservasis::create([
            'id_area'         => $area->id_area,
            'plat_nomor'      => strtoupper(preg_replace('/\s+/', ' ', trim($request->plat_nomor))),
            'pemilik'         => trim($request->pemilik ?? '') ?: null,
            'kontak'          => trim($request->kontak ?? '') ?: null,
            'waktu_datang'    => $request->waktu_datang,
            'status'          => 'menunggu',
            'waktu_pengajuan' => now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('reservasi.create')
            ->with('success', 'Reservasi terkirim! Nomor pengajuan R-' . str_pad((string) $reservasi->id_reservasi, 5, '0', STR_PAD_LEFT)
                . '. Petugas akan mengonfirmasi ketersediaan slot.');
    }

    /**
     * Staff list of reservations with confirm/cancel actions.
     */
    public function index(Request $request)
    {
        $status = (string) $request->query('status', 'menunggu');

        $query = parkir_reservasis::with('area')->orderByDesc('waktu_pengajuan');

        if (in_array($status, ['menunggu', 'dikonfirmasi', 'dibatalkan'], true)) {
            $query->where('status', $status);
        }

        return view('reservasi.index', [
            'reservasis' => $query->get(),
            'status'     => $status,
            'canManage'  => in_array(session('auth_user.role') ?? '', ['admin', 'petugas'], true),
        ]);
    }

    /**
     * Confirm a pending reservation.
     */
    public function confirm(Request $request, string $id)
    {
        $this->authorizeManage();

        $reservasi = parkir_reservasis::find($id);

        if (! $reservasi || $reservasi->status !== 'menunggu') {
            return redirect()->route('reservasi.index')
                ->with('error', 'Reservasi tidak ditemukan atau sudah diproses.');
        }

        $reservasi->update(['status' => 'dikonfirmasi']);

        $this->log($request, 'Mengonfirmasi reservasi R-' . str_pad((string) $reservasi->id_reservasi, 5, '0', STR_PAD_LEFT)
            . ' untuk ' . $reservasi->plat_nomor);

        return redirect()->route('reservasi.index')->with('success', 'Reservasi dikonfirmasi.');
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Request $request, string $id)
    {
        $this->authorizeManage();

        $reservasi = parkir_reservasis::find($id);

        if (! $reservasi || in_array($reservasi->status, ['dibatalkan'], true)) {
            return redirect()->route('reservasi.index')
                ->with('error', 'Reservasi tidak ditemukan atau sudah dibatalkan.');
        }

        $reservasi->update(['status' => 'dibatalkan']);

        $this->log($request, 'Membatalkan reservasi R-' . str_pad((string) $reservasi->id_reservasi, 5, '0', STR_PAD_LEFT)
            . ' untuk ' . $reservasi->plat_nomor);

        return redirect()->route('reservasi.index')->with('success', 'Reservasi dibatalkan.');
    }

    private function authorizeManage()
    {
        if (! in_array(session('auth_user.role') ?? '', ['admin', 'petugas'], true)) {
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
