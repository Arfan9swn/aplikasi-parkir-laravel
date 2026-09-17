@extends('layouts.app')

@section('title', 'park.')
@section('page', 'transaksi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Tiket</h1>
            <p class="mt-1 text-sm text-slate-500">Semua tiket yang pernah terbit — aktif maupun selesai.</p>
        </div>
        <form method="GET" action="{{ url('/transaksi') }}" class="flex flex-wrap items-center gap-3">
            <input name="q" type="text" value="{{ $q }}" placeholder="Cari plat atau nomor tiket…"
                   class="w-60 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            <div class="flex items-center gap-1 text-xs">
                <span class="text-slate-400">Filter:</span>
                @php
                    $bq = http_build_query(['q' => $q]);
                @endphp
                <a href="{{ url('/transaksi?' . http_build_query(['q' => $q, 'status' => 'all'])) }}"
                   class="rounded-full px-3 py-1.5 font-semibold {{ $status === 'all' ? 'bg-primary-500 text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                <a href="{{ url('/transaksi?' . http_build_query(['q' => $q, 'status' => 'masuk'])) }}"
                   class="rounded-full px-3 py-1.5 font-semibold {{ $status === 'masuk' ? 'bg-primary-500 text-white' : 'bg-slate-100 text-slate-600' }}">Aktif</a>
                <a href="{{ url('/transaksi?' . http_build_query(['q' => $q, 'status' => 'keluar'])) }}"
                   class="rounded-full px-3 py-1.5 font-semibold {{ $status === 'keluar' ? 'bg-primary-500 text-white' : 'bg-slate-100 text-slate-600' }}">Selesai</a>
            </div>
            <button type="submit"
                    class="rounded-xl bg-primary-500 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">Terapkan</button>
        </form>
    </div>

    <p class="mt-4 text-xs text-slate-400">{{ $tickets->count() }} tiket ditampilkan</p>

    <div class="mt-3 overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3 font-semibold">Tiket</th>
                        <th class="px-4 py-3 font-semibold">Plat</th>
                        <th class="px-4 py-3 font-semibold">Area</th>
                        <th class="px-4 py-3 font-semibold">Petugas</th>
                        <th class="px-4 py-3 font-semibold">Masuk</th>
                        <th class="px-4 py-3 font-semibold">Keluar</th>
                        <th class="px-4 py-3 font-semibold">Durasi</th>
                        <th class="px-4 py-3 font-semibold">Biaya</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $t)
                        <tr class="border-b border-primary-100 last:border-0">
                            <td class="px-4 py-3 font-mono">P-{{ str_pad($t->id_parkir, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2 font-mono">{{ $t->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $t->area->nama_area ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $t->user->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $t->waktu_masuk ? \Carbon\Carbon::parse($t->waktu_masuk)->format('d M · H:i') : '-' }}</td>
                            <td class="px-4 py-2">{{ $t->waktu_keluar ? \Carbon\Carbon::parse($t->waktu_keluar)->format('d M · H:i') : '-' }}</td>
                            <td class="px-4 py-2">{{ $t->durasi_jam ?? ($t->status === 'masuk' ? '—' : '-') }}</td>
                            <td class="px-4 py-2 font-mono">{{ $t->biaya_total ? 'Rp ' . number_format($t->biaya_total, 0, ',', '.') : '—' }}</td>
                            <td class="px-4 py-2">
                                @if ($t->status === 'masuk')
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Aktif</span>
                                @else
                                    <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-300">Belum ada tiket.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
