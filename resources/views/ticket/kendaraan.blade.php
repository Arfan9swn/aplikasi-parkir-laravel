@extends('layouts.app')

@section('title', 'Data Kendaraan — ParkEase')
@section('page', 'kendaraan')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-500">Semua kendaraan yang terdaftar di gerbang.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <input id="vehicle-search" type="text" placeholder="Cari nomor polisi…"
                   class="w-56 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            <button id="vehicle-add" type="button"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Kendaraan
            </button>
        </div>
    </div>

    <div id="vehicle-grid" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="skeleton h-44 rounded-2xl"></div>
        <div class="skeleton h-44 rounded-2xl"></div>
        <div class="skeleton h-44 rounded-2xl"></div>
    </div>

    <div id="vehicle-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="pop w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 id="vehicle-modal-title" class="text-lg font-bold text-slate-800">Tambah Kendaraan</h3>
                <button type="button" id="vehicle-close" aria-label="Tutup"
                    class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="vehicle-form" class="mt-5 space-y-4" novalidate>
                <div>
                    <label for="vehicle-form-plate" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor Polisi</label>
                    <input id="vehicle-form-plate" type="text" required
                        class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 font-mono text-sm uppercase text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="cth: B 1234 ABC" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="vehicle-form-type" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis</label>
                        <select id="vehicle-form-type"
                            class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100">
                            <option value="motor">Motor</option>
                            <option value="mobil" selected>Mobil</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="vehicle-form-color" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Warna</label>
                        <input id="vehicle-form-color" type="text"
                            class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                            placeholder="cth: Hitam" />
                    </div>
                </div>
                <div>
                    <label for="vehicle-form-owner" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pemilik</label>
                    <input id="vehicle-form-owner" type="text"
                        class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="cth: Budi Santoso" />
                </div>
                <div>
                    <label for="vehicle-form-user" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Petugas / Pemilik Data</label>
                    <select id="vehicle-form-user"
                        class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100">
                        <option value="">Memuat…</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" id="vehicle-cancel"
                        class="flex-1 rounded-xl border border-primary-200 bg-white py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-primary-50">
                        Batal
                    </button>
                    <button id="vehicle-submit" type="submit"
                        class="flex-1 rounded-xl bg-primary-500 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:opacity-60">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection