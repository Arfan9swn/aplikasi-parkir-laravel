@extends('layouts.app')

@section('title', 'park.')
@section('page', 'kendaraan')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('ticket.kendaraan') }}"
               class="text-xs font-semibold text-primary-600 transition hover:text-primary-700">&larr; Data Kendaraan</a>
            <h1 class="mt-1 font-mono text-2xl font-bold text-ink">{{ $vehicle->plat_nomor }}</h1>
            <p class="num mt-1 text-sm text-slate-600">
                {{ ucfirst($vehicle->jenis_kendaraan) }} · {{ $vehicle->warna }} · {{ $vehicle->pemilik }}
                — data oleh {{ $vehicle->user->nama_lengkap ?? '-' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if ($sedangParkir)
                <span class="state bg-emerald-100 text-emerald-800">Sedang parkir</span>
            @else
                <span class="state bg-slate-200 text-slate-700">Tidak di tempat</span>
            @endif
            @if ($canManage)
                <a href="{{ route('ticket.kendaraan.edit', $vehicle->id_kendaraan) }}" class="btn btn-quiet">
                    Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Ringkasan semua kunjungan --}}
    <div class="sheet mb-6">
        <div class="cols-ruled">
            <div>
                <p class="figure-label">Total Kunjungan</p>
                <p class="figure mt-1">{{ $kunjungan }}</p>
                <p class="figure-note mt-0.5">Sejak pertama terdaftar</p>
            </div>
            <div>
                <p class="figure-label">Total Durasi</p>
                <p class="figure mt-1">{{ $totalJam }} <span class="text-sm font-bold">jam</span></p>
                <p class="figure-note mt-0.5">Akumulasi seluruh kunjungan</p>
            </div>
            <div>
                <p class="figure-label">Rata-rata</p>
                <p class="figure mt-1">{{ $rataRata }} <span class="text-sm font-bold">jam</span></p>
                <p class="figure-note mt-0.5">Per kunjungan</p>
            </div>
            <div>
                <p class="figure-label">Total Biaya</p>
                <p class="figure mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Dari tiket yang sudah selesai</p>
            </div>
        </div>
    </div>

    @forelse ($perArea as $area)
        @php $avgArea = $area['kunjungan'] > 0 ? round($area['total_jam'] / $area['kunjungan'], 1) : 0; @endphp
        <div class="sheet mb-5 overflow-hidden">
            <div class="sheet-head">
                <div class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-primary-500 text-white">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-semibold text-ink">{{ $area['nama_area'] }}</p>
                        <p class="num text-[11px] text-slate-600">
                            {{ $area['kunjungan'] }} kunjungan · {{ $area['total_jam'] }} jam total · rata-rata {{ $avgArea }} jam
                            · Rp {{ number_format($area['total_biaya'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @if ($area['sedang_parkir'])
                    <span class="state bg-emerald-100 text-emerald-800">Ada di area ini</span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="ledger">
                    <thead>
                        <tr>
                            <th>Tiket</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th class="text-right">Lama Parkir</th>
                            <th class="text-right">Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($area['visits'] as $v)
                            <tr>
                                <td class="font-mono font-semibold text-primary-700">P-{{ str_pad($v['ticket']->id_parkir, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="num text-slate-600">
                                    {{ $v['ticket']->waktu_masuk ? \Carbon\Carbon::parse($v['ticket']->waktu_masuk)->format('d M Y · H:i') : '-' }}
                                </td>
                                <td class="num text-slate-600">
                                    {{ $v['ticket']->waktu_keluar ? \Carbon\Carbon::parse($v['ticket']->waktu_keluar)->format('d M Y · H:i') : '—' }}
                                </td>
                                <td class="num text-right font-semibold">{{ $v['durasi'] }} jam</td>
                                <td class="num text-right">
                                    {{ $v['biaya'] > 0 ? 'Rp ' . number_format($v['biaya'], 0, ',', '.') : '—' }}
                                </td>
                                <td>
                                    @if ($v['ongoing'])
                                        <span class="state bg-emerald-100 text-emerald-800">Masih parkir</span>
                                    @else
                                        <span class="state bg-slate-200 text-slate-700">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="sheet p-3">
            <p class="notice">
                Kendaraan ini belum pernah tercatat parkir, jadi belum ada riwayat yang bisa dibuka. Riwayat terisi setelah ada tiket atas nomor polisi ini.
            </p>
        </div>
    @endforelse
</div>
@endsection