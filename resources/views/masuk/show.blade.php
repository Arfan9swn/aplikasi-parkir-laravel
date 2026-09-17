@extends('layouts.app')

@section('title', 'park.')
@section('page', 'masuk')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="print-area">
            <h1 class="text-2xl font-bold text-slate-800">Tiket Berhasil Terbit</h1>
            <div class="mx-auto mt-6 max-w-md rounded-2xl border-2 border-dashed border-primary-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold tracking-widest text-slate-400">TIKET PARKIR</span>
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-primary-500 text-xs font-bold text-white">P</span>
                </div>

                <div class="mt-2 text-center">
                    <p class="text-xs font-semibold tracking-widest text-slate-400">NOMOR TIKET</p>
                    <p class="font-mono text-3xl font-extrabold tracking-widest text-primary-700">P-{{ str_pad($ticket->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Plat</p>
                        <p class="font-mono font-semibold text-slate-800">{{ $ticket->kendaraan->plat_nomor }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Jenis</p>
                        <p class="font-semibold text-slate-800">{{ ucfirst($ticket->kendaraan->jenis_kendaraan) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Warna</p>
                        <p class="text-slate-800">{{ $ticket->kendaraan->warna }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Pemilik</p>
                        <p class="text-slate-800">{{ $ticket->kendaraan->pemilik }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-slate-400">Area</p>
                        <p class="font-semibold text-slate-800">{{ $ticket->area->nama_area }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-slate-400">Waktu Masuk</p>
                        <p class="font-mono font-semibold text-slate-800">{{ \Carbon\Carbon::parse($ticket->waktu_masuk)->format('d M Y · H:i') }}</p>
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
                    <a href="{{ url('/masuk') }}"
                       class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                        + Kendaraan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
