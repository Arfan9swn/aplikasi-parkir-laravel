@extends('layouts.app')

@section('title', 'park.')
@section('page', 'kendaraan')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-500">Semua kendaraan yang terdaftar di gerbang. Klik nomor polisi untuk melihat riwayat parkirnya.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ url('/kendaraan') }}" class="flex items-center gap-2">
                <input name="q" type="text" value="{{ $q }}" placeholder="Cari nomor polisi…"
                       class="w-56 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                <button type="submit"
                        class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                    Cari
                </button>
            </form>
            @if ($canManage)
                <a href="{{ route('ticket.tarif') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                    Jenis &amp; Tarif
                </a>
                <a href="{{ route('ticket.kendaraan.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Kendaraan
                </a>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3 font-semibold">Plat</th>
                        <th class="px-4 py-3 font-semibold">Jenis</th>
                        <th class="px-4 py-3 font-semibold">Warna</th>
                        <th class="px-4 py-3 font-semibold">Pemilik</th>
                        <th class="px-4 py-3 font-semibold">Petugas / Pemilik Data</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        @if ($canManage)
                            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $v)
                        @php
                            $parked = isset($parkedNow[$v->id_kendaraan]);
                        @endphp
                        <tr class="border-b border-primary-100 last:border-0">
                            <td class="px-4 py-2">
                                <a href="{{ route('ticket.kendaraan.show', $v->id_kendaraan) }}"
                                   class="font-mono font-semibold text-primary-700 transition hover:text-primary-800 hover:underline">{{ $v->plat_nomor }}</a>
                            </td>
                            <td class="px-4 py-2">{{ ucfirst($v->jenis_kendaraan) }}</td>
                            <td class="px-4 py-2">{{ $v->warna }}</td>
                            <td class="px-4 py-2">{{ $v->pemilik }}</td>
                            <td class="px-4 py-2">{{ $v->user->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if ($parked)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Di tempat</span>
                                @else
                                    <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Keluar</span>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="px-4 py-2 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('ticket.kendaraan.show', $v->id_kendaraan) }}"
                                           class="rounded-lg border border-primary-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                                            Riwayat
                                        </a>
                                        <a href="{{ route('ticket.kendaraan.edit', $v->id_kendaraan) }}"
                                           class="rounded-lg border border-primary-200 bg-white px-2.5 py-1 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ url('/kendaraan/' . $v->id_kendaraan) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg border border-red-200 bg-white px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 7 : 6 }}" class="px-4 py-10 text-center text-sm text-slate-300">Belum ada kendaraan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection