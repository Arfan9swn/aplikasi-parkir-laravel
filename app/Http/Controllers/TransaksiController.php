<?php

namespace App\Http\Controllers;

use App\Models\parkir_transaksis;
use App\Support\SortHelper;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Server-rendered ticket history with search + status + sort filters.
     *
     * Sortable columns: waktu_masuk, waktu_keluar, biaya_total, durasi_jam.
     */
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $allowed = ['waktu_masuk', 'waktu_keluar', 'biaya_total', 'durasi_jam'];
        $sort    = SortHelper::field((string) $request->query('sort'), $allowed);
        $dir     = SortHelper::direction((string) $request->query('dir'));

        $query = parkir_transaksis::with(['kendaraan', 'tarif', 'user', 'area']);

        if (in_array($status, ['masuk', 'keluar'], true)) {
            $query->where('status', $status);
        }

        // Default ordering only when no explicit sort is requested.
        if (! $request->has('sort')) {
            $query->orderByDesc('waktu_masuk');
        } else {
            $query->orderBy($sort, $dir);
        }

        $all = $query->get()->values();

        if ($q !== '') {
            $needle = strtoupper($q);
            $all = $all->filter(function ($t) use ($needle) {
                $plate = strtoupper((string) ($t->kendaraan ? $t->kendaraan->plat_nomor : ''));
                $no    = 'P-' . str_pad((string) $t->id_parkir, 6, '0', STR_PAD_LEFT);
                return str_contains($plate . ' ' . $no, $needle);
            })->values();
        }

        return view('transaksi.index', [
            'tickets' => $all,
            'q'       => $q,
            'status'  => $status,
            'sort'    => $sort,
            'dir'     => $dir,
            'allowed' => $allowed,
        ]);
    }
}