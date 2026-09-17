<?php

namespace App\Http\Controllers;

use App\Models\parkir_kendaraans;
use App\Models\parkir_logs;
use App\Models\parkir_tarifs;
use App\Models\parkir_transaksis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TarifController extends Controller
{
    /**
     * Vehicle types and their hourly rate.
     *
     * tb_tarif *is* the list of types the gate accepts, so a row added here
     * immediately becomes selectable in /masuk.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $tarifs = parkir_tarifs::all();

        if ($q !== '') {
            $needle = strtolower($q);
            $tarifs = $tarifs
                ->filter(fn ($t) => str_contains(strtolower((string) $t->jenis_kendaraan), $needle))
                ->values();
        }

        // How much each type is actually used — a type nobody parks is safe to remove.
        $vehicles = parkir_kendaraans::all();
        $tickets  = parkir_transaksis::all();

        $usage = [];

        foreach ($tarifs as $t) {
            $rows = $tickets->where('id_tarif', $t->id_tarif);

            $usage[$t->id_tarif] = [
                'vehicles' => $vehicles->where('jenis_kendaraan', $t->jenis_kendaraan)->count(),
                'tickets'  => $rows->count(),
                'parked'   => $rows->where('status', 'masuk')->count(),
                'revenue'  => (float) $rows->where('status', 'keluar')->sum('biaya_total'),
            ];
        }

        return view('tarif.index', [
            'tarifs'    => $tarifs,
            'usage'     => $usage,
            'q'         => $q,
            'canManage' => $this->canManage(),
        ]);
    }

    public function create()
    {
        $this->authorizeManage();

        return view('tarif.create', [
            'item'  => null,
            'inUse' => 0,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validator = Validator::make($request->all(), [
            'jenis_kendaraan' => 'required|string|max:50',
            'tarif_per_jam'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.tarif.create')->withInput()->withErrors($validator);
        }

        $jenis = $this->normalize($request->jenis_kendaraan);

        if ($this->typeExists($jenis)) {
            return redirect()->route('ticket.tarif.create')
                ->withInput()
                ->with('error', 'Jenis kendaraan "' . ucfirst($jenis) . '" sudah ada. Pilih nama lain atau ubah tarif yang sudah terdaftar.');
        }

        $tarif = parkir_tarifs::create([
            'jenis_kendaraan' => $jenis,
            'tarif_per_jam'   => round((float) $request->tarif_per_jam, 2),
        ]);

        $this->log(
            $request,
            'Menambahkan jenis kendaraan "' . ucfirst($jenis) . '" dengan tarif Rp '
                . number_format((float) $tarif->tarif_per_jam, 0, ',', '.') . '/jam'
        );

        return redirect()->route('ticket.tarif')->with('success', 'Jenis kendaraan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $this->authorizeManage();

        $tarif = parkir_tarifs::find($id);

        if (! $tarif) {
            return redirect()->route('ticket.tarif')->with('error', 'Tarif tidak ditemukan.');
        }

        return view('tarif.edit', [
            'item'  => $tarif,
            'inUse' => $this->kendaraanCount($tarif->jenis_kendaraan),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeManage();

        $tarif = parkir_tarifs::find($id);

        if (! $tarif) {
            return redirect()->route('ticket.tarif')->with('error', 'Tarif tidak ditemukan.');
        }

        $validator = Validator::make($request->all(), [
            'jenis_kendaraan' => 'required|string|max:50',
            'tarif_per_jam'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticket.tarif.edit', $id)->withInput()->withErrors($validator);
        }

        $lama  = (string) $tarif->jenis_kendaraan;
        $jenis = $this->normalize($request->jenis_kendaraan);

        if ($this->typeExists($jenis, $tarif->id_tarif)) {
            return redirect()->route('ticket.tarif.edit', $id)
                ->withInput()
                ->with('error', 'Jenis kendaraan "' . ucfirst($jenis) . '" sudah dipakai oleh tarif lain.');
        }

        $tarif->update([
            'jenis_kendaraan' => $jenis,
            'tarif_per_jam'   => round((float) $request->tarif_per_jam, 2),
        ]);

        // tb_kendaraan stores a type by *name*, so a rename must cascade or the
        // fleet would be left pointing at a type that no longer exists.
        $disesuaikan = 0;

        if ($lama !== $jenis) {
            $disesuaikan = parkir_kendaraans::where('jenis_kendaraan', $lama)
                ->update(['jenis_kendaraan' => $jenis]);
        }

        $activity = 'Memperbarui jenis kendaraan "' . ucfirst($lama) . '"';

        if ($lama !== $jenis) {
            $activity .= ' menjadi "' . ucfirst($jenis) . '"';
        }

        $activity .= ' — tarif Rp ' . number_format((float) $tarif->tarif_per_jam, 0, ',', '.') . '/jam';

        if ($disesuaikan > 0) {
            $activity .= ' (' . $disesuaikan . ' kendaraan disesuaikan)';
        }

        $this->log($request, $activity);

        $success = 'Jenis kendaraan berhasil diperbarui.';

        if ($disesuaikan > 0) {
            $success .= ' ' . $disesuaikan . ' kendaraan dipindahkan ke jenis baru.';
        }

        return redirect()->route('ticket.tarif')->with('success', $success);
    }

    public function destroy(Request $request, string $id)
    {
        $this->authorizeManage();

        $tarif = parkir_tarifs::find($id);

        if (! $tarif) {
            return redirect()->route('ticket.tarif')->with('error', 'Tarif tidak ditemukan.');
        }

        $jenis = (string) $tarif->jenis_kendaraan;

        if (parkir_transaksis::where('id_tarif', $tarif->id_tarif)->exists()) {
            return redirect()->route('ticket.tarif')
                ->with('error', 'Jenis "' . ucfirst($jenis) . '" tidak dapat dihapus karena masih memiliki riwayat transaksi.');
        }

        $dipakai = $this->kendaraanCount($jenis);

        if ($dipakai > 0) {
            return redirect()->route('ticket.tarif')
                ->with('error', 'Jenis "' . ucfirst($jenis) . '" masih dipakai oleh ' . $dipakai . ' kendaraan. Pindahkan kendaraan tersebut ke jenis lain terlebih dahulu.');
        }

        $tarif->delete();
        $this->log($request, 'Menghapus jenis kendaraan "' . ucfirst($jenis) . '"');

        return redirect()->route('ticket.tarif')->with('success', 'Jenis kendaraan berhasil dihapus.');
    }

    // ------------------------------------------------------------
    // helpers
    // ------------------------------------------------------------

    /**
     * Types are stored lower-cased: /masuk matches tb_tarif and tb_kendaraan
     * on the exact name, and the seeded rows are 'motor' / 'mobil' / 'lainnya'.
     */
    private function normalize(string $value): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($value)));
    }

    /**
     * Case-insensitive duplicate check, so "Truk" cannot be added next to "truk".
     */
    private function typeExists(string $jenis, $exceptId = null): bool
    {
        return parkir_tarifs::all()
            ->when($exceptId, fn ($rows) => $rows->where('id_tarif', '!=', $exceptId))
            ->contains(fn ($t) => strtolower((string) $t->jenis_kendaraan) === $jenis);
    }

    private function kendaraanCount($jenis): int
    {
        return parkir_kendaraans::where('jenis_kendaraan', $jenis)->count();
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
