@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@php($fmt = 'd M Y · H:i')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="sheet p-6 sm:p-8">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-ink">Kendaraan Keluar</h1>
            <p class="mt-1 text-sm text-slate-600">Konfirmasi durasi &amp; biaya parkir.</p>
        </div>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Tiket</dt>
                <dd class="font-mono font-bold text-primary-700">P-{{ str_pad($ticket->id_parkir, 6, '0', STR_PAD_LEFT) }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Plat</dt>
                <dd class="text-ink"><x-plate :value="$ticket->kendaraan->plat_nomor ?? null" /></dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Area</dt>
                <dd class="font-semibold text-ink">{{ $ticket->area->nama_area ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Jenis</dt>
                <dd class="text-ink">{{ ucfirst($ticket->kendaraan->jenis_kendaraan ?? '-') }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Jam Masuk</dt>
                <dd class="num font-mono text-ink">{{ \Carbon\Carbon::parse($ticket->waktu_masuk)->format($fmt) }}</dd>
            </div>
            <div>
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Sekarang</dt>
                <dd class="num font-mono text-ink">{{ \Carbon\Carbon::now()->format($fmt) }}</dd>
            </div>
            <div class="col-span-2 border-y border-rule py-3">
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Durasi Parkir (pembulatan ke atas, min. 1 jam)</dt>
                <dd class="num font-display text-3xl font-bold text-ink">{{ $durasi }} jam</dd>
                @if (($ticket->tarif->tarif_per_jam ?? 0) > 0)
                    <p class="num mt-1 text-xs text-slate-600">
                        Tarif {{ ucfirst($ticket->tarif->jenis_kendaraan ?? '-') }}:
                        Rp {{ number_format((float) $ticket->tarif->tarif_per_jam, 0, ',', '.') }}/jam
                    </p>
                @endif
            </div>
            <div class="col-span-2">
                <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Total yang harus dibayar</dt>
                <dd class="num font-display text-3xl font-bold text-ink">Rp {{ number_format((float) ($fee ?? 0), 0, ',', '.') }}</dd>
            </div>
        </dl>
        <form method="POST" action="{{ url('/keluar/pay') }}" class="mt-6 flex items-center justify-end gap-2 border-t border-rule pt-4">
            @csrf
            <input type="hidden" name="id_parkir" value="{{ $ticket->id_parkir }}">
            <a href="{{ $backUrl }}" class="btn btn-quiet">Batal / cari lain</a>
            <button type="submit" class="btn btn-primary">Bayar &amp; selesaikan</button>
        </form>
    </div>
</div>
@endsection
