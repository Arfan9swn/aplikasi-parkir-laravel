@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@php($fmt = 'd M Y · H:i')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="sheet tear p-6 sm:p-8">
        <div class="mb-6 flex items-center justify-between gap-3 border-b border-rule pb-4">
            <div>
                <p class="font-display text-xs font-bold uppercase tracking-[0.2em] text-slate-600">park.</p>
                <h1 class="font-display text-2xl font-bold text-ink">Tiket Selesai</h1>
            </div>
            <button type="button" onclick="window.print()" class="btn btn-primary">Cetak</button>
        </div>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Nomor Tiket</dt>
                <dd class="num font-mono text-lg font-bold text-primary-700">P-{{ str_pad($receipt->id_parkir, 6, '0', STR_PAD_LEFT) }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Plat</dt>
                <dd class="text-ink"><x-plate :value="$receipt->kendaraan->plat_nomor ?? null" /></dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Area</dt>
                <dd class="font-semibold text-ink">{{ $receipt->area->nama_area ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Petugas</dt>
                <dd class="font-semibold text-ink">{{ $receipt->user->nama_lengkap ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Jam Masuk</dt>
                <dd class="num font-mono text-ink">{{ \Carbon\Carbon::parse($receipt->waktu_masuk)->format($fmt) }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Jam Keluar</dt>
                <dd class="num font-mono text-ink">{{ \Carbon\Carbon::parse($receipt->waktu_keluar)->format($fmt) }}</dd>
            </div>
            <div class="col-span-2 border-t border-rule pt-4">
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Durasi Parkir</dt>
                <dd class="num font-display text-2xl font-bold text-ink">{{ $receipt->durasi_jam }} jam</dd>
            </div>
            <div class="col-span-2 border-t border-rule pt-4">
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Total Bayar</dt>
                <dd class="num font-display text-3xl font-bold text-ink">Rp {{ number_format((float) ($receipt->biaya_total ?? 0), 0, ',', '.') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex justify-end border-t border-rule pt-4">
            <a href="{{ url('/keluar') }}" class="btn btn-quiet">Transaksi baru</a>
        </div>
    </div>
</div>
@endsection
