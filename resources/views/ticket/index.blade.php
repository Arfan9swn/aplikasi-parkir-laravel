@extends('layouts.app')

@section('title', 'park.')
@section('page', 'transaksi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Tiket</h1>
            <p class="mt-1 text-sm text-slate-500">Semua tiket yang pernah terbit — aktif maupun selesai.</p>
        </div>
        <div class="flex items-center gap-3">
            <input id="tx-search" type="text" placeholder="Cari plat atau nomor tiket…"
                   class="w-56 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            <div id="tx-filters" class="flex items-center gap-2">
                <button type="button" data-filter="all" class="filter-pill is-active rounded-full px-3.5 py-1.5 text-xs font-semibold">Semua</button>
                <button type="button" data-filter="masuk" class="filter-pill rounded-full px-3.5 py-1.5 text-xs font-semibold">Aktif</button>
                <button type="button" data-filter="keluar" class="filter-pill rounded-full px-3.5 py-1.5 text-xs font-semibold">Selesai</button>
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-primary-100 px-5 py-3">
            <h2 class="text-sm font-bold text-slate-700">Riwayat Tiket</h2>
            <span id="tx-count" class="text-xs text-slate-400">—</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3 font-semibold">Tiket</th>
                        <th class="px-4 py-3 font-semibold">Kendaraan</th>
                        <th class="px-4 py-3 font-semibold">Area</th>
                        <th class="px-4 py-3 font-semibold">Jam Masuk</th>
                        <th class="px-4 py-3 font-semibold">Jam Keluar</th>
                        <th class="px-4 py-3 font-semibold">Durasi</th>
                        <th class="px-4 py-3 font-semibold">Biaya</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody id="tx-body">
                    <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-300">Memuat tiket…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection