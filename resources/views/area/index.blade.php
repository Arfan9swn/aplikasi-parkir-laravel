@extends('layouts.app')

@section('title', 'park.')
@section('page', 'area')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Area Parkir</h1>
            <p class="mt-1 text-sm text-slate-500">Kepadatan parkir per area — setiap area memiliki petugas khusus.</p>
        </div>
        @if ($canManage)
            <a href="{{ route('ticket.area.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Area
            </a>
        @endif
    </div>

    @php
        $totalSpots = $areas->sum('kapasitas');
        $totalUsed  = $areas->sum('terisi');
        $totalFree  = max(0, $totalSpots - $totalUsed);
    @endphp
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Slot</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format((int) $totalSpots, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Kapasitas gabungan</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Terisi</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format((int) $totalUsed, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-300">{{ $totalSpots > 0 ? (int) min(100, ($totalUsed / $totalSpots) * 100) : 0 }}% padatnya</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kosong</p>
            <p class="mt-1.5 text-2xl font-extrabold text-emerald-600">{{ number_format((int) $totalFree, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Slot tersedia</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Area Terdaftar</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ $areas->count() }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Area aktif</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3 font-semibold">Area</th>
                        <th class="px-4 py-3 font-semibold">Petugas</th>
                        <th class="px-4 py-3 font-semibold">Kapasitas</th>
                        <th class="px-4 py-3 font-semibold">Terisi / Kosong</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        @if ($canManage)
                            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($areas as $a)
                        @php
                            $free = max(0, $a->kapasitas - $a->terisi);
                            $pct  = $a->kapasitas > 0 ? min(100, ($a->terisi / $a->kapasitas) * 100) : 0;
                            $full = $a->kapasitas > 0 && $a->terisi >= $a->kapasitas;
                        @endphp
                        <tr class="border-b border-primary-100 last:border-0">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-800">{{ $a->nama_area }}</p>
                            </td>
                            <td class="px-4 py-2 text-slate-600">{{ $a->petugas->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $a->kapasitas }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-24 rounded-full bg-primary-100">
                                        <div class="h-full rounded-full {{ $full ? 'bg-red-400' : 'bg-primary-500' }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="font-mono text-xs text-slate-600">{{ $a->terisi }}/{{ $a->kapasitas }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                @if ($a->id_user)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Teredaftar</span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Tanpa petugas</span>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="px-4 py-2 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('ticket.area.edit', $a->id_area) }}"
                                           class="rounded-lg border border-primary-200 bg-white px-2.5 py-1 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ url('/area/' . $a->id_area) }}">
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
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-300">Belum ada area parkir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
