@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@section('content')
<div class="mx-auto max-w-5xl">
    <h1 class="font-display text-2xl font-bold text-ink">Kendaraan Keluar — Pembayaran</h1>
    <p class="mt-1 text-sm text-slate-600">
        Masukkan nomor polisi, atau langsung klik kendaraan yang sedang parkir di bawah ini.
    </p>

    {{-- Plate lookup (unchanged) — carries the active area filter through. --}}
    <form method="POST" action="{{ url('/keluar/check') . $areaQuery }}" autocomplete="off" class="mt-6">
        @csrf
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label for="keluar-plate" class="text-xs font-semibold text-slate-600">Nomor Polisi</label>
                <input id="keluar-plate" name="plat_nomor" required type="text" placeholder="B 1234 ABC"
                       value="{{ old('plat_nomor') }}" class="field mt-1 font-mono text-base" />
            </div>
            <button type="submit" class="btn btn-primary">Cari Tiket</button>
        </div>
    </form>

    {{-- One-click list: everything still parked. Each card is a link to the checkout. --}}
    <div class="mt-8 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="font-display text-lg font-bold text-ink">Sedang Parkir</h2>
            <p class="num mt-0.5 text-xs text-slate-600">
                {{ $parked->count() }} kendaraan menunggu keluar — klik salah satu untuk memprosesnya.
            </p>
        </div>

        <x-filter-select :options="$filters" :current="$areaFilter" base-url="{{ url('/keluar') }}"
                         param="area" label="Filter area parkir" />
    </div>

    @if ($parked->isEmpty())
        <div class="sheet mt-4 overflow-hidden">
            <p class="notice m-3">
                {{ $areaFilter === ''
                    ? 'Belum ada kendaraan yang sedang parkir, jadi tidak ada yang perlu diproses.'
                    : 'Tidak ada kendaraan pada area ini. Pilih Semua area untuk melihat seluruh kendaraan yang masih parkir.' }}
            </p>
        </div>
    @else
        <div class="sheet mt-4 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ledger">
                    <thead>
                        <tr>
                            <th>Plat</th>
                            <th>Kendaraan</th>
                            <th>Area</th>
                            <th>Masuk</th>
                            <th class="text-right">Durasi</th>
                            <th class="text-right">Estimasi</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parked as $row)
                            @php $t = $row['ticket']; @endphp
                            <tr>
                                <td>
                                    <a href="{{ url('/keluar/' . $t->id_parkir) . $areaQuery }}"
                                       class="font-mono font-semibold text-primary-700 transition hover:text-primary-800 hover:underline">{{ $t->kendaraan->plat_nomor ?? '-' }}</a>
                                </td>
                                <td class="text-slate-600">{{ ucfirst($t->kendaraan->jenis_kendaraan ?? '-') }}</td>
                                <td>{{ $t->area->nama_area ?? '-' }}</td>
                                <td class="num text-slate-600">{{ \Carbon\Carbon::parse($t->waktu_masuk)->format('d M · H:i') }}</td>
                                <td class="num text-right">{{ $row['durasi'] }} jam</td>
                                <td class="num text-right font-semibold">Rp {{ number_format((float) $row['fee'], 0, ',', '.') }}</td>
                                <td class="text-right">
                                    <a href="{{ url('/keluar/' . $t->id_parkir) . $areaQuery }}" class="btn btn-quiet">Proses keluar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
