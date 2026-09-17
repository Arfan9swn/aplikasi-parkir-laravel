@extends('layouts.app')

@section('title', 'park.')
@section('page', 'reservasi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Reservasi Masuk</h1>
            <p class="mt-1 text-sm text-slate-500">Pengajuan slot dari pengunjung — konfirmasi bila slot tersedia.</p>
        </div>
        <div class="flex items-center gap-1 text-xs">
            <span class="text-slate-400">Filter:</span>
            @foreach (['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'dibatalkan' => 'Dibatalkan', 'all' => 'Semua'] as $val => $lbl)
                <a href="{{ route('reservasi.index', ['status' => $val]) }}"
                   class="filter-pill rounded-full px-3 py-1.5 font-semibold {{ $status === $val ? 'is-active' : '' }}">{{ $lbl }}</a>
            @endforeach
        </div>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($reservasis as $r)
            @php
                $menunggu = $r->status === 'menunggu';
                $label = ['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'dibatalkan' => 'Dibatalkan'][$r->status] ?? $r->status;
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5">
                        <p class="font-mono text-xs font-semibold tracking-widest text-slate-400">R-{{ str_pad((string) $r->id_reservasi, 5, '0', STR_PAD_LEFT) }}</p>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $menunggu ? 'bg-amber-100 text-amber-800' : ($r->status === 'dikonfirmasi' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600') }}">
                            {{ $label }}
                        </span>
                    </div>
                    <p class="mt-1 font-mono text-base font-bold text-slate-800">{{ $r->plat_nomor }}</p>
                    <p class="mt-0.5 text-sm text-slate-500">
                        {{ $r->area->nama_area ?? '-' }} · Datang
                        {{ $r->waktu_datang ? \Carbon\Carbon::parse($r->waktu_datang)->format('d M Y · H:i') : '-' }}
                    </p>
                    @if ($r->pemilik || $r->kontak)
                        <p class="mt-0.5 text-xs text-slate-400">{{ $r->pemilik }}@if($r->kontak) · {{ $r->kontak }}@endif</p>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if ($menunggu && $canManage)
                        <form method="POST" action="{{ route('reservasi.confirm', $r->id_reservasi) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                Konfirmasi
                            </button>
                        </form>
                    @endif
                    @if ($r->status !== 'dibatalkan' && $canManage)
                        <form method="POST" action="{{ route('reservasi.cancel', $r->id_reservasi) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-lg border border-red-200 bg-white px-3.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-primary-100 bg-white p-10 text-center text-sm text-slate-400 shadow-sm">
                Tidak ada reservasi {{ $status === 'all' ? '' : 'dengan status ini.' }}
            </div>
        @endforelse
    </div>
@endsection
