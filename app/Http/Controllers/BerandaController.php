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

        return view('beranda', compact('areas', 'spots', 'occupied', 'active', 'revenue'));
    }
}