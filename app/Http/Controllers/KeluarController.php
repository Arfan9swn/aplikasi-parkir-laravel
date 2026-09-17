<?php

namespace App\Http\Controllers;

use App\Models\parkir_areas;
use App\Models\parkir_logs;
use App\Models\parkir_transaksis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeluarController extends Controller
{
    /**
     * Show the check-out form.
     */
    public function create()
    {
        return view('keluar.create');
    }

    /**
     * Look up the active ticket by plate number and preview duration + fee.
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plat_nomor' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.keluar')->withInput()->withErrors($validator);
        }

        $plate = strtoupper(preg_replace('/\s+/', ' ', trim($request->plat_nomor)));

        $ticket = parkir_transaksis::with(['kendaraan', 'tarif', 'area'])
            ->where('status', 'masuk')
            ->whereHas('kendaraan', function ($query) use ($plate) {
                $query->where('plat_nomor', $plate);
            })
            ->orderByDesc('waktu_masuk')
            ->first();

        if (! $ticket) {
            return redirect()->route('ticket.keluar')
                ->withInput()
                ->with('error', 'Tidak ada tiket aktif untuk nomor polisi ' . $plate . '.');
        }

        $durasi = $this->durasi($ticket->waktu_masuk);

        return view('keluar.show', [
            'ticket' => $ticket,
            'durasi' => $durasi,
            'fee'    => $this->biaya($ticket, $durasi),
        ]);
    }

    /**
     * Settle the ticket: stamp the check-out time, compute the fee and free the slot.
     */
    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_parkir' => 'required|integer|exists:tb_transaksi,id_parkir',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.keluar')->withInput()->withErrors($validator);
        }

        $ticket = parkir_transaksis::with(['kendaraan', 'tarif', 'area'])->find($request->id_parkir);

        if (! $ticket || $ticket->status !== 'masuk') {
            return redirect()->route('ticket.keluar')
                ->with('error', 'Tiket tidak ditemukan atau sudah dibayar.');
        }

        $durasi = $this->durasi($ticket->waktu_masuk);
        $biaya  = $this->biaya($ticket, $durasi);

        $ticket->update([
            'waktu_keluar' => now()->format('Y-m-d H:i:s'),
            'durasi_jam'   => $durasi,
            'biaya_total'  => $biaya,
            'status'       => 'keluar',
        ]);

        $area = parkir_areas::find($ticket->id_area);

        if ($area && (int) $area->terisi > 0) {
            $area->decrement('terisi');
        }

        $this->log(
            $request,
            'Menyelesaikan tiket ' . $this->ticketNo($ticket->id_parkir)
                . ' untuk ' . ($ticket->kendaraan->plat_nomor ?? '-')
        );

        return view('keluar.receipt', [
            'receipt' => $ticket->fresh(['kendaraan', 'tarif', 'user', 'area']),
        ]);
    }

    // ------------------------------------------------------------
    // helpers
    // ------------------------------------------------------------

    /**
     * Billed hours: rounded up, minimum 1 hour.
     */
    private function durasi($waktuMasuk): int
    {
        $masuk  = \Carbon\Carbon::parse($waktuMasuk);
        $keluar = \Carbon\Carbon::now();

        $menit = (int) ceil(abs($masuk->diffInSeconds($keluar, false)) / 60);

        return max(1, (int) ceil($menit / 60));
    }

    private function biaya(parkir_transaksis $ticket, int $durasiJam): float
    {
        $tarifPerJam = (float) ($ticket->tarif->tarif_per_jam ?? 0);

        return round($durasiJam * $tarifPerJam, 2);
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