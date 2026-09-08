@extends('layouts.app')

@section('title', 'Kendaraan Keluar — ParkEase')
@section('page', 'keluar')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold text-slate-800">Kendaraan Keluar — Bayar &amp; Pulang</h1>
        <p class="mt-1 text-sm text-slate-500">Masukkan nomor polisi kendaraan yang akan keluar.</p>

        {{-- Cari tiket ------------------------------------------------- --}}
        <form id="exit-search-form" autocomplete="off" class="mt-6 flex items-end gap-3">
            <div class="flex-1">
                <label class="text-xs font-medium text-slate-500">Nomor Polisi</label>
                <input id="exit-plate" required type="text" placeholder="B 1234 ABC"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            </div>
            <button id="find-ticket" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary-600">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                Cari Tiket
            </button>
        </form>

        {{-- Tiket tidak ditemukan --------------------------------------- --}}
        <div id="exit-empty" class="hidden">
            <div class="mt-7 rounded-2xl border border-primary-100 bg-white p-8 text-center shadow-sm">
                <p class="text-sm text-slate-500">Tidak ada tiket aktif untuk plat</p>
                <p class="my-1 font-mono font-bold text-primary-700" id="empty-plate">—</p>
                <p class="mt-3 text-xs text-slate-400">Kendaraan mungkin sudah keluar, atau belum pernah masuk.</p>
                <a href="/masuk" class="mt-4 inline-block text-sm font-semibold text-primary-600 hover:text-primary-700">← Terbitkan tiket baru</a>
            </div>
        </div>

        {{-- Tiket aktif ------------------------------------------------- --}}
        <div id="exit-active" class="hidden">
            <div class="mt-7 rounded-2xl border border-primary-100 bg-white shadow-sm">
                <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-3">
                    <div class="rounded-xl bg-primary-50 px-4 py-3">
                        <p class="text-xs text-slate-400">Tiket</p>
                        <p id="exit-no" class="font-mono text-xl font-bold text-primary-700">P-000000</p>
                    </div>
                    <div class="rounded-xl bg-primary-50 px-4 py-3 sm:col-span-2">
                        <p class="text-xs text-slate-400">Kendaraan</p>
                        <p id="exit-plate-show" class="font-mono text-xl font-bold text-slate-800">—</p>
                        <p id="exit-type" class="text-xs text-slate-500"></p>
                        <p id="exit-owner" class="text-xs text-slate-500"></p>
                    </div>
                </div>

                <div class="border-t border-primary-100 px-5 py-4 text-sm">
                    <div class="grid grid-cols-2 gap-y-2">
                        <div><span class="text-slate-400">Area</span><span id="exit-area" class="ml-2 font-medium text-slate-800">—</span></div>
                        <div class="text-right"><span class="text-slate-400">Masuk</span><span id="exit-time-in" class="ml-2 font-medium text-slate-800">—</span></div>
                        <div class="col-span-2"><span class="text-slate-400">Tarif</span><span id="exit-rate" class="ml-2 font-medium text-slate-800">—</span></div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-primary-100 bg-amber-50 px-5 py-4">
                    <div>
                        <p class="text-xs text-slate-500">Lama parkir</p>
                        <p id="live-elapsed" class="font-mono text-xl font-bold text-amber-700">00:00:00</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Total bayar</p>
                        <p id="live-fee" class="font-mono text-2xl font-extrabold text-amber-700">Rp 0</p>
                    </div>
                </div>

                <div class="flex items-center justify-end border-t border-primary-100 p-4">
                    <button id="pay-btn"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-600">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                        Bayar &amp; Selesai
                    </button>
                </div>
            </div>
        </div>

        {{-- Struk ------------------------------------------------------- --}}
        <div id="exit-receipt" class="hidden print-area">
            <div class="mx-auto mt-7 max-w-md rounded-2xl border-2 border-dashed border-primary-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold tracking-widest text-slate-400">STRUK PARKIR</span>
                    <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-500 text-xs font-bold text-white">P</span>
                </div>

                <p class="mt-2 text-center font-mono text-2xl font-extrabold tracking-widest text-primary-700" id="rcpt-no">P-000000</p>

                <div class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div><p class="text-xs text-slate-400">Plat</p><p id="rcpt-plate" class="font-mono font-semibold text-slate-800">—</p></div>
                    <div><p class="text-xs text-slate-400">Jenis</p><p id="rcpt-type" class="text-slate-800">—</p></div>
                    <div><p class="text-xs text-slate-400">Area</p><p id="rcpt-area" class="font-semibold text-slate-800">—</p></div>
                    <div><p class="text-xs text-slate-400">Durasi</p><p id="rcpt-dur" class="font-semibold text-slate-800">—</p></div>
                    <div class="col-span-2"><p class="text-xs text-slate-400">Jam Masuk</p><p id="rcpt-in" class="font-mono text-slate-800">—</p></div>
                    <div class="col-span-2"><p class="text-xs text-slate-400">Jam Keluar</p><p id="rcpt-out" class="font-mono text-slate-800">—</p></div>
                </div>

                <div class="mt-5 flex items-center justify-between rounded-xl bg-primary-50 px-4 py-3">
                    <span class="text-sm font-medium text-slate-600">Total dibayar</span>
                    <span id="rcpt-amount" class="text-2xl font-extrabold text-primary-700">—</span>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3 border-t border-primary-100 pt-3">
                    <button type="button" onclick="window.print()"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-primary-700 px-4 py-2 text-xs font-bold text-white transition hover:bg-primary-800">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/>
                        </svg>
                        Cetak Struk
                    </button>
                    <button id="rcpt-done" type="button"
                            class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection