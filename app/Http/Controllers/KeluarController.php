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
     * Show the check-out form: the plate lookup plus a clickable list of every
     * vehicle that is still parked, so a petugas can settle one in a single click.
     */
    public function create(Request $request)
    {
        $areaFilter = trim((string) $request->query('area', ''));
        $mine       = $this->petugasAreaIds();

        $query = parkir_transaksis::with(['kendaraan', 'tarif', 'area'])
            ->where('status', 'masuk');

        if ($areaFilter === 'mine') {
            // No assigned area? Match nothing rather than leaking every ticket.
            $query->whereIn('id_area', $mine !== [] ? $mine : [-1]);
        } elseif ($areaFilter !== '' && ctype_digit($areaFilter)) {
            $query->where('id_area', (int) $areaFilter);
        }

        // Longest-parked first: the oldest ticket is the one to settle next.
        $parked = $query->orderBy('waktu_masuk')->get()
            ->map(function (parkir_transaksis $ticket) {
                $durasi = $this->durasi($ticket->waktu_masuk);

                return [
                    'ticket' => $ticket,
                    'durasi' => $durasi,
                    'fee'    => $this->biaya($ticket, $durasi),
                ];
            });

        $filters = [['value' => '', 'label' => 'Semua area']];

        if ($mine !== []) {
            $filters[] = ['value' => 'mine', 'label' => 'Area saya'];
        }

        foreach (parkir_areas::orderBy('nama_area')->get() as $area) {
            $filters[] = ['value' => (string) $area->id_area, 'label' => $area->nama_area];
        }

        return view('keluar.create', [
            'parked'     => $parked,
            'filters'    => $filters,
            'areaFilter' => $areaFilter,
            'areaQuery'  => $areaFilter === '' ? '' : '?area=' . urlencode($areaFilter),
        ]);
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

        return $this->preview($ticket, $request);
    }

    /**
     * Preview a check-out straight from the parked list — one click, no typing.
     */
    public function show(Request $request, string $id)
    {
        $ticket = parkir_transaksis::with(['kendaraan', 'tarif', 'area'])
            ->where('status', 'masuk')
            ->find($id);

        if (! $ticket) {
            return redirect()->route('ticket.keluar')
                ->with('error', 'Tiket tidak ditemukan atau kendaraan sudah keluar.');
        }

        return $this->preview($ticket, $request);
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
     * Render the duration + fee confirmation screen for one ticket.
     * The active area filter is carried through so "back" returns to it.
     */
    private function preview(parkir_transaksis $ticket, Request $request)
    {
        $durasi     = $this->durasi($ticket->waktu_masuk);
        $areaFilter = trim((string) $request->query('area', ''));

        return view('keluar.show', [
            'ticket'  => $ticket,
            'durasi'  => $durasi,
            'fee'     => $this->biaya($ticket, $durasi),
            'backUrl' => url('/keluar' . ($areaFilter === '' ? '' : '?area=' . urlencode($areaFilter))),
        ]);
    }

    /**
     * Area ids assigned to the logged-in petugas (empty for admin / owner).
     */
    private function petugasAreaIds(): array
    {
        $idUser = session('auth_user.id_user');

        if (! $idUser) {
            return [];
        }

        return parkir_areas::where('id_user', $idUser)->pluck('id_area')->all();
    }

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