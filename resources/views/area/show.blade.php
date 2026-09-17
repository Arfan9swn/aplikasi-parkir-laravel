@extends('layouts.app')

@section('title', 'park.')
@section('page', 'area')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('ticket.area') }}" class="text-xs font-semibold text-primary-600 transition hover:text-primary-700">← Semua area</a>
            <h1 class="mt-1 text-2xl font-bold text-slate-800">Area {{ $area->nama_area }}</h1>
            <p class="mt-1 text-sm text-slate-500">Kendaraan yang sedang berada di area ini saat ini.</p>
        </div>
        <a href="{{ route('reservasi.create', ['area' => $area->id_area]) }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
            Reservasi slot di area ini
        </a>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kapasitas</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ $area->kapasitas }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Slot tersedia</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Terisi</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ $area->terisi }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Sedang parkir</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kosong</p>
            <p class="mt-1.5 text-2xl font-extrabold text-emerald-600">{{ max(0, $area->kapasitas - $area->terisi) }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Bisa dipakai</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Petugas</p>
            <p class="mt-1.5 truncate text-lg font-bold text-slate-800">{{ $area->petugas->nama_lengkap ?? '—' }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Penanggung jawab area</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3 font-semibold">Kendaraan</th>
                        <th class="px-4 py-3 font-semibold">Jenis</th>
                        <th class="px-4 py-3 font-semibold">Pemilik</th>
                        <th class="px-4 py-3 font-semibold">Masuk</th>
                        <th class="px-4 py-3 font-semibold">Lama Parkir</th>
                        <th class="px-4 py-3 font-semibold">Perkiraan Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parkir as $p)
                        <tr class="border-b border-primary-100 last:border-0">
                            <td class="px-4 py-3 font-mono font-semibold text-slate-800">{{ $p->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="px-4 py-2">{{ ucfirst($p->kendaraan->jenis_kendaraan ?? '-') }}</td>
                            <td class="px-4 py-2">{{ $p->kendaraan->pemilik ?? '-' }}</td>
                            <td class="px-4 py-2 text-slate-600">{{ $p->waktu_masuk->format('d M · H:i') }}</td>
                            <td class="px-4 py-2">
                                <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700">{{ $p->durasi_jam }} jam</span>
                            </td>
                            <td class="px-4 py-2 font-mono">Rp {{ number_format($p->estimasi, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-300">Belum ada kendaraan di area ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($canManage)
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <div>
                <p class="text-sm font-semibold text-slate-700">Panel petugas</p>
                <p class="mt-0.5 text-xs text-slate-500">
                    {{ $reservasiMenunggu }} reservasi menunggu konfirmasi di area ini.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('ticket.area.edit', $area->id_area) }}"
                   class="rounded-lg border border-primary-200 bg-white px-3.5 py-2 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">Edit Area</a>
                <a href="{{ route('reservasi.index') }}"
                   class="rounded-lg bg-primary-500 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-primary-600">Reservasi Masuk</a>
            </div>
        </div>
    @endif
</div>
@endsection
