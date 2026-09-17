@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@php($fmt = 'd M Y · H:i')

@section('content')
<div class="mx-auto max-w-3xl">
    @isset($receipt)
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
    @else
@isset($ticket)
            <div class="rounded-2xl border border-primary-100 bg-white p-8 shadow-sm">
                <div class="mb-6 flex items-center gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Kendaraan Keluar</h1>
                        <p class="text-sm text-slate-500">Konfirmasi durasi &amp; biaya parkir.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-x-6 gap-y-5 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Tiket</p>
                        <p class="font-mono font-bold text-primary-700">P-{{ str_pad($ticket->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Plat</p>
                        <p class="font-mono font-semibold text-slate-800">{{ $ticket->kendaraan->plat_nomor ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Area</p>
                        <p class="font-semibold text-slate-800">{{ $ticket->area->nama_area ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Jenis</p>
                        <p class="text-slate-800">{{ ucfirst($ticket->kendaraan->jenis_kendaraan ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Jam Masuk</p>
                        <p class="font-mono text-slate-800">{{ \Carbon\Carbon::parse($ticket->waktu_masuk)->format($fmt) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Sekarang</p>
                        <p class="font-mono text-slate-800">{{ \Carbon\Carbon::now()->format($fmt) }}</p>
                    </div>
                    <div class="col-span-2 rounded-xl border border-primary-100 bg-primary-50 p-4">
                        <p class="text-xs text-slate-500">Durasi Parkir (pembulatan ke atas, min. 1 jam)</p>
                        <p class="font-mono text-3xl font-extrabold text-primary-700">{{ $durasi }} jam</p>
                        @if (($ticket->tarif->tarif_per_jam ?? 0) > 0)
                            <p class="mt-0.5 text-xs text-slate-500">
                                Tarif {{ ucfirst($ticket->tarif->jenis_kendaraan ?? '-') }}:
                                Rp {{ number_format((float) $ticket->tarif->tarif_per_jam, 0, ',', '.') }}/jam
                            </p>
                        @endif
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-slate-400">Total yang harus dibayar</p>
                        <p class="font-mono text-2xl font-extrabold text-emerald-600">Rp {{ number_format((float) ($fee ?? 0), 0, ',', '.') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ url('/keluar/pay') }}" class="mt-6 flex items-center justify-end gap-3">
                    @csrf
                    <input type="hidden" name="id_parkir" value="{{ $ticket->id_parkir }}">
                    <a href="{{ url('/keluar') }}"
                       class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                        Batal / cari lain
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-600">
                        Bayar &amp; selesaikan
                    </button>
                </form>
            </div>
        @else
            <h1 class="text-2xl font-bold text-slate-800">Kendaraan Keluar — Pembayaran</h1>
            <p class="mt-1 text-sm text-slate-500">Masukkan nomor polisi kendaraan yang akan keluar.</p>
            <form method="POST" action="{{ url('/keluar/check') }}" autocomplete="off" class="mt-6">
                @csrf
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <label for="keluar-plate" class="text-xs font-medium text-slate-500">Nomor Polisi</label>
                        <input id="keluar-plate" name="plat_nomor" required type="text" placeholder="B 1234 ABC"
                               value="{{ old('plat_nomor') }}"
                               class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                    </div>
                    <button type="submit"
                        class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
                        Cari Tiket
                    </button>
                </div>
            </form>
        @endisset
    @endisset
</div>
@endsection
