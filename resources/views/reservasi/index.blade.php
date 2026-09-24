@extends('layouts.app')

@section('title', 'park.')
@section('page', 'reservasi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Reservasi Masuk</h1>
            <p class="mt-1 text-sm text-slate-600">Pengajuan slot dari pengunjung — konfirmasi bila slot tersedia.</p>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-xs text-slate-600">Filter:</span>
            @foreach (['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'dibatalkan' => 'Dibatalkan', 'all' => 'Semua'] as $val => $lbl)
                <a href="{{ route('reservasi.index', ['status' => $val]) }}"
                   class="tab {{ $status === $val ? 'is-active' : '' }}"
                   @if ($status === $val) aria-current="page" @endif>{{ $lbl }}</a>
            @endforeach
        </div>
    </div>

    <div class="sheet mt-6">
        @forelse ($reservasis as $r)
            @php
                $menunggu = $r->status === 'menunggu';
                $label = ['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'dibatalkan' => 'Dibatalkan'][$r->status] ?? $r->status;
            @endphp
            <div class="clause flex-wrap items-center justify-between">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5">
                        <p class="num font-mono text-xs font-semibold tracking-widest text-slate-600">R-{{ str_pad((string) $r->id_reservasi, 5, '0', STR_PAD_LEFT) }}</p>
                        <span class="state {{ $menunggu ? 'bg-amber-100 text-amber-800' : ($r->status === 'dikonfirmasi' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700') }}">
                            {{ $label }}
                        </span>
                    </div>
                    <p class="mt-1 font-mono text-base font-bold text-ink">{{ $r->plat_nomor }}</p>
                    <p class="num mt-0.5 text-sm text-slate-600">
                        {{ $r->area->nama_area ?? '-' }} · Datang
                        {{ $r->waktu_datang ? \Carbon\Carbon::parse($r->waktu_datang)->format('d M Y · H:i') : '-' }}
                    </p>
                    @if ($r->pemilik || $r->kontak)
                        <p class="mt-0.5 text-xs text-slate-600">{{ $r->pemilik }}@if($r->kontak) · {{ $r->kontak }}@endif</p>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if ($menunggu && $canManage)
                        <form method="POST" action="{{ route('reservasi.confirm', $r->id_reservasi) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Konfirmasi</button>
                        </form>
                    @endif
                    @if ($r->status !== 'dibatalkan' && $canManage)
                        <form method="POST" action="{{ route('reservasi.cancel', $r->id_reservasi) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Batalkan</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="notice m-3">
                @if ($status === 'all')
                    Belum ada pengajuan reservasi. Pengajuan masuk lewat halaman Reservasi yang diisi pengunjung.
                @else
                    Tidak ada reservasi dengan status ini. Pilih Semua untuk melihat seluruh pengajuan.
                @endif
            </p>
        @endforelse
    </div>
@endsection
