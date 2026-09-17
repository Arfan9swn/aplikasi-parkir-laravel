<?php

namespace App\Http\Controllers;

use App\Models\parkir_areas;
use App\Models\parkir_kendaraans;
use App\Models\parkir_logs;
use App\Models\parkir_tarifs;
use App\Models\parkir_transaksis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasukController extends Controller
{
    /**
     * Show the check-in form.
     */
    public function create()
    {
        $areas = parkir_areas::with('petugas')->get();
        $tarifs = parkir_tarifs::all();

        return view('masuk.create', compact('areas', 'tarifs'));
    }

    /**
     * Issue a parking ticket (server-side, no JavaScript).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plat_nomor' => 'required|string',
            'jenis_kendaraan' => 'required|string|in:'.implode(',', parkir_tarifs::pluck('jenis_kendaraan')->all()),
            'id_area' => 'required|integer|exists:tb_area_parkir,id_area',
            'warna' => 'nullable|string',
            'pemilik' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $area = parkir_areas::with('petugas')->find($request->id_area);

        if (! $area || ! $area->petugas) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Area tersebut belum memiliki petugas. Hubungi admin untuk menugaskan petugas.');
        }

        if ((int) $area->terisi >= (int) $area->kapasitas) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Area parkir sudah penuh, silakan pilih area lain.');
        }

        $tarif = parkir_tarifs::where('jenis_kendaraan', $request->jenis_kendaraan)->first();

        if (! $tarif) {
            return redirect()->back()->withInput()->with('error', 'Belum ada tarif untuk jenis kendaraan tersebut.');
        }

        $plate = strtoupper(preg_replace('/\s+/', ' ', trim($request->plat_nomor)));

        $kendaraan = parkir_kendaraans::where('plat_nomor', $plate)->first();

        if (! $kendaraan) {
            $kendaraan = parkir_kendaraans::create([
                'id_user'         => $area->petugas->id_user,
                'plat_nomor'      => $plate,
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'warna'           => trim($request->warna ?? '') ?: '—',
                'pemilik'         => trim($request->pemilik ?? '') ?: 'Tanpa nama',
            ]);
        }

        $ticket = parkir_transaksis::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'id_tarif'     => $tarif->id_tarif,
            'id_user'      => $area->petugas->id_user,
            'id_area'      => $area->id_area,
            'waktu_masuk'  => now()->format('Y-m-d H:i:s'),
            'waktu_keluar' => null,
            'durasi_jam'   => null,
            'biaya_total'  => null,
            'status'       => 'masuk',
        ]);

        $area->increment('terisi');

        $this->log($request, 'Menerbitkan tiket ' . $this->ticketNo($ticket->id_parkir) . ' untuk ' . $plate);

        return view('masuk.show', [
            'areas'  => parkir_areas::with('petugas')->get(),
            'tarifs' => parkir_tarifs::all(),
            'ticket' => parkir_transaksis::with(['kendaraan', 'tarif', 'area'])->find($ticket->id_parkir),
        ]);
    }

    private function ticketNo(int $id): string
    {
        return 'P-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
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