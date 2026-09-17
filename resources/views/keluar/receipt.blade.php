@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@php($fmt = 'd M Y · H:i')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="rounded-2xl border border-primary-100 bg-white p-8 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">Tiket Selesai</h1>
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-1.5 rounded-xl bg-primary-700 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-primary-800">
                Cetak
            </button>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-5 text-sm">
            <div>
                <p class="text-xs text-slate-400">Nomor Tiket</p>
                <p class="font-mono text-lg font-bold text-primary-700">P-{{ str_pad($receipt->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Plat</p>
                <p class="font-mono font-semibold text-slate-800">{{ $receipt->kendaraan->plat_nomor ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Area</p>
                <p class="font-semibold text-slate-800">{{ $receipt->area->nama_area ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Petugas</p>
                <p class="font-semibold text-slate-800">{{ $receipt->user->nama_lengkap ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Jam Masuk</p>
                <p class="font-mono text-slate-800">{{ \Carbon\Carbon::parse($receipt->waktu_masuk)->format($fmt) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Jam Keluar</p>
                <p class="font-mono text-slate-800">{{ \Carbon\Carbon::parse($receipt->waktu_keluar)->format($fmt) }}</p>
            </div>
            <div class="col-span-2 border-t border-primary-100 pt-4">
                <p class="text-xs text-slate-400">Durasi Parkir</p>
                <p class="font-mono text-2xl font-extrabold text-slate-800">{{ $receipt->durasi_jam }} jam</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-slate-400">Total Bayar</p>
                <p class="font-mono text-2xl font-extrabold text-emerald-600">Rp {{ number_format((float) ($receipt->biaya_total ?? 0), 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ url('/keluar') }}"
               class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                Transaksi baru
            </a>
        </div>
    </div>
</div>
@endsection
