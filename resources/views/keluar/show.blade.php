@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@php($fmt = 'd M Y · H:i')

@section('content')
<div class="mx-auto max-w-3xl">
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
            <a href="{{ $backUrl }}"
               class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                Batal / cari lain
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-600">
                Bayar &amp; selesaikan
            </button>
        </form>
    </div>
</div>
@endsection
