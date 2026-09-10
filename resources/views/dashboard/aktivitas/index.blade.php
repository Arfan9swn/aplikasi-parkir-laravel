@extends('layouts.app')

@section('title', 'Log — park.')
@section('page', 'log')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pusat Log</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau aktivitas pengguna dan log sistem secara langsung.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button id="log-live-badge" type="button" title="Klik untuk menghentikan / melanjutkan pemantauan otomatis"
                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-200">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 live-dot"></span>
                <span id="log-live-label">LIVE · 5 dtk</span>
            </button>
            <select id="log-interval"
                class="rounded-xl border border-primary-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 outline-none transition focus:border-primary-400">
                <option value="3">3 detik</option>
                <option value="5" selected>5 detik</option>
                <option value="10">10 detik</option>
                <option value="30">30 detik</option>
                <option value="0">Manual</option>
            </select>
            <button id="log-refresh" type="button"
                class="inline-flex items-center gap-1.5 rounded-xl bg-primary-500 px-4 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:opacity-60">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 1 1-2.64-6.36"/>
                    <path d="M21 3v6h-6"/>
                </svg>
                Segarkan
            </button>
        </div>
    </div>

    {{-- stat cards --}}
    <section class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Log</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="log-stat-total">
                <span class="skeleton inline-block h-7 w-16 rounded"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Semua aktivitas tercatat</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Hari Ini</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="log-stat-today">
                <span class="skeleton inline-block h-7 w-16 rounded"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Aktivitas sejak tengah malam</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Petugas Aktif</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="log-stat-users">
                <span class="skeleton inline-block h-7 w-16 rounded"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Pengguna berbeda</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Error Sistem</p>
            <p class="mt-1.5 text-2xl font-extrabold text-red-600" id="log-stat-errors">
                <span class="skeleton inline-block h-7 w-16 rounded"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Pada file laravel.log</p>
        </div>
    </section>
{{-- tabs --}}
    <div class="mt-8 flex items-center gap-2" id="log-tabs">
        <button type="button" data-tab="aktivitas" class="tab-pill is-active rounded-full px-4 py-1.5 text-xs font-semibold">Log Aktivitas</button>
        <button type="button" data-tab="sistem" class="tab-pill rounded-full px-4 py-1.5 text-xs font-semibold">Log Sistem</button>
    </div>

    {{-- panel: aktivitas --}}
    <section id="panel-aktivitas" class="mt-4">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <input id="log-search" type="text" placeholder="Cari aktivitas, nama, atau username…"
                class="w-full max-w-sm rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            <span id="log-new-chip" class="hidden rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-800 transition">+0 log baru</span>
        </div>

        <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-primary-100 px-5 py-3">
                <h2 class="text-sm font-bold text-slate-700">Jejak Aktivitas</h2>
                <span id="log-count" class="text-xs text-slate-400">—</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                            <th class="px-4 py-3 font-semibold">Waktu</th>
                            <th class="px-4 py-3 font-semibold">Petugas</th>
                            <th class="px-4 py-3 font-semibold">Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody id="log-activity-body">
                        <tr><td colspan="3" class="px-4 py-10 text-center text-sm text-slate-300">Memuat log…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
{{-- panel: sistem --}}
    <section id="panel-sistem" class="mt-4 hidden">
        <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-primary-100 px-5 py-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-700">File Log Server</h2>
                    <p class="text-[11px] text-slate-400" id="log-file-meta">Memuat…</p>
                </div>
                <span id="log-file-size" class="text-xs text-slate-400"></span>
            </div>
            <div id="log-file-body"
                class="max-h-[540px] overflow-auto rounded-b-2xl bg-slate-900 p-4 font-mono text-[11px] leading-relaxed text-slate-300">
                <span class="text-slate-500">Menunggu data…</span>
            </div>
        </div>
    </section>
@endsection

