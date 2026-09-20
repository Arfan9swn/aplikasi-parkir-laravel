<?php

namespace App\Http\Controllers;

use App\Models\parkir_areas;
use App\Models\parkir_transaksis;

class BerandaController extends Controller
{
    public function index()
    {
        $areas = parkir_areas::all();
        $tx = parkir_transaksis::all();

        $spots    = $areas->sum('kapasitas');
        $occupied = $areas->sum('terisi');
        $active   = $tx->where('status', 'masuk')->count();

        $today = date('Y-m-d');
        $revenue = $tx
            ->where('status', 'keluar')
            ->filter(function ($t) use ($today) {
                return $t->waktu_keluar ? substr((string) $t->waktu_keluar, 0, 10) === $today : false;
            })
            ->sum('biaya_total');

        // Chart 1 — vehicles entering per day, last 14 days.
        $daily = collect(range(13, 0))->map(function ($i) use ($tx) {
            $date = now()->subDays($i);

            return [
                'label'   => $date->format('d M'),
                'tickets' => $tx->filter(fn ($t) => optional($t->waktu_masuk)->format('Y-m-d') === $date->format('Y-m-d'))->count(),
            ];
        })->values();

        // Chart 2 — completed-ticket revenue and volume per area.
        $settled = $tx->where('status', 'keluar')->groupBy('id_area');
        $areaPerf = $areas
            ->map(fn ($a) => [
                'name'    => $a->nama_area,
                'revenue' => (float) optional($settled->get($a->id_area))->sum('biaya_total'),
                'tickets' => optional($settled->get($a->id_area))->count() ?? 0,
            ])
            ->sortByDesc('revenue')
            ->values();

        return view('beranda', compact('areas', 'spots', 'occupied', 'active', 'revenue', 'daily', 'areaPerf'));
    }
}