@extends('layouts.app')

@section('title', 'park.')
@section('page', 'kendaraan')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Data Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-600">Semua kendaraan yang terdaftar di gerbang. Klik nomor polisi untuk melihat riwayat parkirnya.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ url('/kendaraan') }}" class="flex items-center gap-2">
                <input name="q" type="text" value="{{ $q }}" placeholder="Cari nomor polisi…" class="field w-56" />
                <button type="submit" class="btn btn-quiet">Cari</button>
            </form>
            @if ($canManage)
                <a href="{{ route('ticket.tarif') }}" class="btn btn-quiet">Jenis &amp; Tarif</a>
                <a href="{{ route('ticket.kendaraan.create') }}" class="btn btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Kendaraan
                </a>
            @endif
        </div>
    </div>

    <div class="sheet overflow-hidden">
        <div class="overflow-x-auto">
            <table class="ledger">
                <thead>
                    <tr>
                        <x-table-sort column="plat_nomor" label="Plat" :allowed="$allowed" />
                        <x-table-sort column="jenis_kendaraan" label="Jenis" :allowed="$allowed" />
                        <x-table-sort column="warna" label="Warna" :allowed="$allowed" />
                        <x-table-sort column="pemilik" label="Pemilik" :allowed="$allowed" />
                        <th>Petugas / Pemilik Data</th>
                        <th>Status</th>
                        @if ($canManage)
                            <th class="text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $v)
                        @php
                            $parked = isset($parkedNow[$v->id_kendaraan]);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('ticket.kendaraan.show', $v->id_kendaraan) }}"
                                   class="font-mono font-semibold text-primary-700 transition hover:text-primary-800 hover:underline">{{ $v->plat_nomor }}</a>
                            </td>
                            <td>{{ ucfirst($v->jenis_kendaraan) }}</td>
                            <td class="text-slate-600">{{ $v->warna }}</td>
                            <td>{{ $v->pemilik }}</td>
                            <td class="text-slate-600">{{ $v->user->nama_lengkap ?? '-' }}</td>
                            <td>
                                @if ($parked)
                                    <span class="state bg-emerald-100 text-emerald-800">Di tempat</span>
                                @else
                                    <span class="state bg-slate-200 text-slate-700">Keluar</span>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('ticket.kendaraan.show', $v->id_kendaraan) }}" class="btn btn-quiet">Riwayat</a>
                                        <a href="{{ route('ticket.kendaraan.edit', $v->id_kendaraan) }}" class="btn btn-quiet">Edit</a>
                                        <form method="POST" action="{{ url('/kendaraan/' . $v->id_kendaraan) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 7 : 6 }}" class="p-0">
                                <p class="notice m-3">
                                    @if ($q !== '')
                                        Tidak ada kendaraan dengan nomor polisi yang cocok. Coba potongan plat yang lebih pendek.
                                    @else
                                        Belum ada kendaraan terdaftar. Data terisi saat petugas menerbitkan tiket di menu Masuk, atau lewat Tambah Kendaraan.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection