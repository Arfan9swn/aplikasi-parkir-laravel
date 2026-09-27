@extends('layouts.app')

@section('title', 'park.')
@section('page', 'masuk')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="print-area">
            <h1 class="font-display text-2xl font-bold text-ink">Tiket Berhasil Terbit</h1>
            <div class="mx-auto mt-6 max-w-md sheet tear px-6 py-5">
                <div class="flex items-baseline justify-between">
                    <span class="font-display text-xs font-bold uppercase tracking-[0.2em] text-slate-600">Tiket Parkir</span>
                    <span class="font-display text-lg font-bold text-primary-700">park.</span>
                </div>

                <div class="mt-3 text-center">
                    <p class="font-display text-[11px] font-bold uppercase tracking-[0.15em] text-slate-600">Nomor Tiket</p>
                    <p class="num mt-1 font-mono text-3xl font-bold tracking-widest text-primary-700">P-{{ str_pad($ticket->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
                </div>

                <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div>
                        <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Plat</dt>
                        <dd class="text-ink"><x-plate :value="$ticket->kendaraan->plat_nomor" /></dd>
                    </div>
                    <div>
                        <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Jenis</dt>
                        <dd class="font-semibold text-ink">{{ ucfirst($ticket->kendaraan->jenis_kendaraan) }}</dd>
                    </div>
                    <div>
                        <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Warna</dt>
                        <dd class="text-ink">{{ $ticket->kendaraan->warna }}</dd>
                    </div>
                    <div>
                        <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Pemilik</dt>
                        <dd class="text-ink">{{ $ticket->kendaraan->pemilik }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Area</dt>
                        <dd class="font-semibold text-ink">{{ $ticket->area->nama_area }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="font-display text-[11px] font-bold uppercase tracking-wide text-slate-600">Waktu Masuk</dt>
                        <dd class="num font-mono font-semibold text-ink">{{ \Carbon\Carbon::parse($ticket->waktu_masuk)->format('d M Y · H:i') }}</dd>
                    </div>
                </dl>

                <div class="mt-4 flex items-center justify-end gap-2 border-t border-rule pt-3">
                    <button type="button" onclick="window.print()" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/>
                        </svg>
                        Cetak Tiket
                    </button>
                    <a href="{{ url('/masuk') }}" class="btn btn-quiet">
                        + Kendaraan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
