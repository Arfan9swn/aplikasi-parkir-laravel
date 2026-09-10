@extends('layouts.app')

@section('title', 'park.')
@section('page', 'masuk')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold text-slate-800">Kendaraan Masuk — Terbitkan Tiket</h1>
        <p class="mt-1 text-sm text-slate-500">Isi nomor polisi, pilih area parkir dan jenis kendaraannya.</p>

        {{-- Formulir --------------------------------------------------- --}}
        <form id="issue-form" autocomplete="off" class="mt-6 space-y-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-xs font-medium text-slate-500">Nomor Polisi</label>
                    <input id="issue-plate" required type="text" placeholder="B 1234 ABC"
                           class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Jenis Kendaraan</label>
                    <select id="issue-type" required
                            class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                        <option value="">— Pilih jenis —</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Warna</label>
                    <input id="issue-color" type="text" placeholder="Contoh: Hitam"
                           class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Nama Pemilik</label>
                    <input id="issue-owner" type="text" placeholder="Contoh: Budi"
                           class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                </div>
                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Area Parkir</label>
                    <select id="issue-area" required
                            class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                        <option value="">— Pilih area —</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between rounded-xl border border-primary-200/60 bg-primary-50 px-4 py-2.5">
                <span class="text-xs text-slate-500">Petugas</span>
                <span id="issue-operator" class="font-medium text-primary-700">—</span>
                <input id="issue-user-id" type="hidden" value="">
            </div>

            <button id="issue-submit" type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary-600">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                </svg>
                Terbitkan Tiket
            </button>
            <button id="issue-submit2" type="button"
                    class="hidden w-full rounded-xl bg-amber-300 px-5 py-3 text-sm font-bold text-primary-800 shadow-sm transition hover:bg-amber-400">
                Terbitkan &amp; Cetak
            </button>
        </form>
    </div>
    {{-- Tiket ------------------------------------------------------- --}}
    <div id="ticket-panel" class="print-area hidden">
        <div class="mx-auto mt-8 max-w-md rounded-2xl border-2 border-dashed border-primary-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest text-slate-400">TIKET PARKIR</span>
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-primary-500 text-xs font-bold text-white">P</span>
            </div>

            <div class="mt-2 text-center">
                <p class="text-xs font-semibold tracking-widest text-slate-400">NOMOR TIKET</p>
                <p id="ticket-no" class="font-mono text-3xl font-extrabold tracking-widest text-primary-700">P-000000</p>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                <div>
                    <p class="text-xs text-slate-400">Plat</p>
                    <p id="ticket-plate" class="font-mono font-semibold text-slate-800">—</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Jenis</p>
                    <p id="ticket-type" class="font-semibold text-slate-800">—</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Warna</p>
                    <p id="ticket-color" class="text-slate-800">—</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Pemilik</p>
                    <p id="ticket-owner" class="text-slate-800">—</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-slate-400">Area</p>
                    <p id="ticket-area" class="font-semibold text-slate-800">—</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-slate-400">Waktu Masuk</p>
                    <p id="ticket-time" class="font-mono font-semibold text-slate-800">—</p>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-3 border-t border-primary-100 pt-3">
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-primary-700 px-4 py-2 text-xs font-bold text-white transition hover:bg-primary-800">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/>
                    </svg>
                    Cetak Tiket
                </button>
                <button id="issue-again" type="button"
                        class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                    + Kendaraan Baru
                </button>
            </div>
        </div>
    </div>
@endsection