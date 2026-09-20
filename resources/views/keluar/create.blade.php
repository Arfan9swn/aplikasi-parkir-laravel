@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@section('content')
<div class="mx-auto max-w-5xl">
    <h1 class="text-2xl font-bold text-slate-800">Kendaraan Keluar — Pembayaran</h1>
    <p class="mt-1 text-sm text-slate-500">
        Masukkan nomor polisi, atau langsung klik kendaraan yang sedang parkir di bawah ini.
    </p>

    {{-- Plate lookup (unchanged) — carries the active area filter through. --}}
    <form method="POST" action="{{ url('/keluar/check') . $areaQuery }}" autocomplete="off" class="mt-6">
        @csrf
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label for="keluar-plate" class="text-xs font-medium text-slate-500">Nomor Polisi</label>
                <input id="keluar-plate" name="plat_nomor" required type="text" placeholder="B 1234 ABC"
                       value="{{ old('plat_nomor') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            </div>
            <button type="submit"
                class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
                Cari Tiket
            </button>
        </div>
    </form>

    {{-- One-click list: everything still parked. Each card is a link to the checkout. --}}
    <div class="mt-10 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Sedang Parkir</h2>
            <p class="mt-0.5 text-xs text-slate-500">
                {{ $parked->count() }} kendaraan menunggu keluar — klik salah satu untuk memprosesnya.
            </p>
        </div>

        <x-filter-select :options="$filters" :current="$areaFilter" base-url="{{ url('/keluar') }}"
                         param="area" label="Filter area parkir" />
    </div>

    @if ($parked->isEmpty())
        <div class="mt-4 rounded-2xl border border-dashed border-primary-200 bg-white px-4 py-12 text-center text-sm text-slate-400">
            {{ $areaFilter === '' ? 'Belum ada kendaraan yang sedang parkir.' : 'Tidak ada kendaraan pada area ini.' }}
        </div>
    @else
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($parked as $row)
                @php $t = $row['ticket']; @endphp
                <a href="{{ url('/keluar/' . $t->id_parkir) . $areaQuery }}"
                   class="group flex flex-col rounded-2xl border border-primary-100 bg-white p-4 shadow-sm transition hover:border-primary-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-mono text-lg font-extrabold tracking-wide text-slate-800">{{ $t->kendaraan->plat_nomor ?? '-' }}</span>
                        <span class="shrink-0 rounded-full bg-primary-100 px-2.5 py-0.5 text-[11px] font-semibold text-primary-700">{{ $row['durasi'] }} jam</span>
                    </div>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ ucfirst($t->kendaraan->jenis_kendaraan ?? '-') }} · {{ $t->area->nama_area ?? '-' }}
                    </p>

                    <div class="mt-3 flex items-center justify-between gap-2 border-t border-primary-100 pt-2">
                        <span class="text-[11px] text-slate-400">Masuk {{ \Carbon\Carbon::parse($t->waktu_masuk)->format('d M · H:i') }}</span>
                        <span class="flex items-baseline gap-1">
                            <span class="text-[11px] text-slate-400">Estimasi</span>
                            <span class="text-xs font-bold text-emerald-600">Rp {{ number_format((float) $row['fee'], 0, ',', '.') }}</span>
                        </span>
                    </div>

                    <span class="mt-2 text-[11px] font-semibold text-primary-600 group-hover:underline">Proses keluar →</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
