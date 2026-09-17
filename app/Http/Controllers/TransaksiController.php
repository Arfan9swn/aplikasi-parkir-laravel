<?php

namespace App\Http\Controllers;

use App\Models\parkir_transaksis;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Server-rendered ticket history with search + status filters.
     */
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $query = parkir_transaksis::with(['kendaraan', 'tarif', 'user', 'area']);

        if (in_array($status, ['masuk', 'keluar'], true)) {
            $query->where('status', $status);
        }

        $all = $query->get()->sortByDesc('waktu_masuk')->values();

        if ($q !== '') {
            $needle = strtoupper($q);
            $all = $all->filter(function ($t) use ($needle) {
                $plate = strtoupper((string) ($t->kendaraan ? $t->kendaraan->plat_nomor : ''));
                $no    = 'P-' . str_pad((string) $t->id_parkir, 6, '0', STR_PAD_LEFT);
                return str_contains($plate . ' ' . $no, $needle);
            })->values();
        }

        return view('ticket.index', [
            'tickets' => $all,
            'q'       => $q,
            'status'  => $status,
        ]);
    }
}