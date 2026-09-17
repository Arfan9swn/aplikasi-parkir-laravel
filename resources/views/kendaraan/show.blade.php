@extends('layouts.app')

@section('title', 'park.')
@section('page', 'kendaraan')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('ticket.kendaraan') }}"
               class="text-xs font-semibold text-primary-600 transition hover:text-primary-700">&larr; Data Kendaraan</a>
            <h1 class="mt-1 font-mono text-2xl font-bold text-slate-800">{{ $vehicle->plat_nomor }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ ucfirst($vehicle->jenis_kendaraan) }} · {{ $vehicle->warna }} · {{ $vehicle->pemilik }}
                — data oleh {{ $vehicle->user->nama_lengkap ?? '-' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if ($sedangParkir)
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Sedang parkir</span>
            @else
                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">Tidak di tempat</span>
            @endif
            @if ($canManage)
                <a href="{{ route('ticket.kendaraan.edit', $vehicle->id_kendaraan) }}"
                   class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                    Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Ringkasan semua kunjungan --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Kunjungan</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ $kunjungan }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Sejak pertama terdaftar</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Durasi</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ $totalJam }} <span class="text-sm font-bold">jam</span></p>
            <p class="mt-1 text-[11px] text-slate-300">Akumulasi seluruh kunjungan</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rata-rata</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ $rataRata }} <span class="text-sm font-bold">jam</span></p>
            <p class="mt-1 text-[11px] text-slate-300">Per kunjungan</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Biaya</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Dari tiket yang sudah selesai</p>
        </div>
    </div>

    @forelse ($perArea as $area)
        @php $avgArea = $area['kunjungan'] > 0 ? round($area['total_jam'] / $area['kunjungan'], 1) : 0; @endphp
        <div class="mb-5 overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-primary-100 bg-primary-50 px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-primary-500 text-white">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">{{ $area['nama_area'] }}</p>
                        <p class="text-[11px] text-slate-500">
                            {{ $area['kunjungan'] }} kunjungan · {{ $area['total_jam'] }} jam total · rata-rata {{ $avgArea }} jam
                            · Rp {{ number_format($area['total_biaya'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @if ($area['sedang_parkir'])
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Ada di area ini</span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-primary-100 text-[11px] uppercase tracking-wider text-slate-400">
                            <th class="px-4 py-2.5 font-semibold">Tiket</th>
                            <th class="px-4 py-2.5 font-semibold">Masuk</th>
                            <th class="px-4 py-2.5 font-semibold">Keluar</th>
                            <th class="px-4 py-2.5 font-semibold">Lama Parkir</th>
                            <th class="px-4 py-2.5 font-semibold">Biaya</th>
                            <th class="px-4 py-2.5 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($area['visits'] as $v)
                            <tr class="border-b border-primary-100 last:border-0">
                                <td class="px-4 py-2 font-mono">P-{{ str_pad($v['ticket']->id_parkir, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-2 font-mono text-slate-600">
                                    {{ $v['ticket']->waktu_masuk ? \Carbon\Carbon::parse($v['ticket']->waktu_masuk)->format('d M Y · H:i') : '-' }}
                                </td>
                                <td class="px-4 py-2 font-mono text-slate-600">
                                    {{ $v['ticket']->waktu_keluar ? \Carbon\Carbon::parse($v['ticket']->waktu_keluar)->format('d M Y · H:i') : '—' }}
                                </td>
                                <td class="px-4 py-2 font-semibold text-slate-800">{{ $v['durasi'] }} jam</td>
                                <td class="px-4 py-2 font-mono">
                                    {{ $v['biaya'] > 0 ? 'Rp ' . number_format($v['biaya'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-4 py-2">
                                    @if ($v['ongoing'])
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Masih parkir</span>
                                    @else
                                        <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-primary-100 bg-white px-4 py-12 text-center shadow-sm">
            <p class="text-sm text-slate-400">Kendaraan ini belum pernah tercatat parkir.</p>
        </div>
    @endforelse
</div>
@endsection