@extends('layouts.app')

@section('title', 'ParkEase — Aplikasi Tiket Parkir')
@section('page', 'beranda')

@section('content')
    <section class="rounded-2xl bg-primary-500 p-8 shadow-sm sm:p-10">

        <h1 class="mt-4 text-3xl font-extrabold text-white sm:text-4xl">
            Masuk. Parkir. Bayar &amp; selesai.
        </h1>
        <p class="mt-3 max-w-xl text-base text-primary-100">
            Aplikasi pencatatan parkir yang sederhana. Cetak tiket saat kendaraan masuk,
            lalu cari lagi dengan nomor polisi saat kendaraan keluar.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="/masuk"
               class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-primary-700 shadow-sm transition hover:bg-primary-50">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                    <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                </svg>
                Masuk Terbitkan Tiket
            </a>
            <a href="/keluar"
               class="inline-flex items-center gap-2 rounded-xl border border-white/40 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                </svg>
                Keluar Bayar &amp; Pulang
            </a>
        </div>

        <p class="mt-6 font-mono text-sm text-white/85" id="hero-clock">—</p>
    </section>

    <section class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                    </svg>
                </span>
                Total Slot
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="stat-spots">
                <span class="skeleton inline-block h-7 w-16 rounded stat-skeleton"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Kapasitas parkir</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                </span>
                Terisi Saat Ini
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="stat-occupied">
                <span class="skeleton inline-block h-7 w-16 rounded stat-skeleton"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Slot terpakai</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                </span>
                Tiket Aktif
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="stat-active">
                <span class="skeleton inline-block h-7 w-16 rounded stat-skeleton"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Sedang parkir</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                    </svg>
                </span>
                Pendapatan Hari Ini
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="stat-revenue">
                <span class="skeleton inline-block h-7 w-16 rounded stat-skeleton"></span>
            </p>
            <p class="mt-1 text-[11px] text-slate-300">Total bayar</p>
        </div>
    </section>

    <section class="mt-10">
        <h2 class="text-xl font-bold text-slate-800">Cara Pakai</h2>
        <div class="mt-5 grid gap-5 md:grid-cols-3">
            <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <p class="grid h-9 w-9 place-items-center rounded-full bg-primary-500 text-sm font-bold text-white">1</p>
                <h3 class="mt-3 font-semibold text-slate-800">Kendaraan masuk</h3>
                <p class="mt-1.5 text-sm text-slate-500">Tulis nomor polisi, pilih area dan jenis kendaraan — tiket masuk langsung jadi.</p>
            </div>
            <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <p class="grid h-9 w-9 place-items-center rounded-full bg-primary-500 text-sm font-bold text-white">2</p>
                <h3 class="mt-3 font-semibold text-slate-800">Pantau langsung</h3>
                <p class="mt-1.5 text-sm text-slate-500">Setiap area menampilkan kepadatan parkir secara langsung, lengkap dengan lama parkir dan perkiraan biaya.</p>
            </div>
            <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <p class="grid h-9 w-9 place-items-center rounded-full bg-primary-500 text-sm font-bold text-white">3</p>
                <h3 class="mt-3 font-semibold text-slate-800">Keluar &amp; bayar</h3>
                <p class="mt-1.5 text-sm text-slate-500">Cari kendaraan lewat nomor polisi, cek durasinya, bayar biayanya, lalu slot langsung kosong lagi.</p>
            </div>
        </div>
    </section>

    <section class="mt-10">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Kondisi Parkir Saat Ini</h2>
            <a href="/area" class="text-sm font-semibold text-primary-600 transition hover:text-primary-700">Lihat semua area →</a>
        </div>
        <div id="area-mini" class="mt-5 space-y-4">
            <div class="skeleton h-4 w-full rounded"></div>
            <div class="skeleton h-4 w-full rounded"></div>
        </div>
    </section>
@endsection