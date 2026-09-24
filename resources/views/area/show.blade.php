@extends('layouts.app')

@section('title', 'park.')
@section('page', 'area')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('ticket.area') }}" class="text-xs font-semibold text-primary-600 transition hover:text-primary-700">← Semua area</a>
            <h1 class="mt-1 font-display text-2xl font-bold text-ink">Area {{ $area->nama_area }}</h1>
            <p class="mt-1 text-sm text-slate-600">Kendaraan yang sedang berada di area ini saat ini.</p>
        </div>
        <a href="{{ route('reservasi.create', ['area' => $area->id_area]) }}" class="btn btn-primary">
            Reservasi slot di area ini
        </a>
    </div>

    <div class="sheet mb-6">
        <div class="cols-ruled">
            <div>
                <p class="figure-label">Kapasitas</p>
                <p class="figure mt-1">{{ $area->kapasitas }}</p>
                <p class="figure-note mt-0.5">Slot tersedia</p>
            </div>
            <div>
                <p class="figure-label">Terisi</p>
                <p class="figure mt-1">{{ $area->terisi }}</p>
                <p class="figure-note mt-0.5">Sedang parkir</p>
            </div>
            <div>
                <p class="figure-label">Kosong</p>
                <p class="figure mt-1">{{ max(0, $area->kapasitas - $area->terisi) }}</p>
                <p class="figure-note mt-0.5">Bisa dipakai</p>
            </div>
            <div>
                <p class="figure-label">Petugas</p>
                <p class="mt-1 truncate text-lg font-semibold text-ink">{{ $area->petugas->nama_lengkap ?? '—' }}</p>
                <p class="figure-note mt-0.5">Penanggung jawab area</p>
            </div>
        </div>
    </div>

    <div class="sheet overflow-hidden">
        <div class="overflow-x-auto">
            <table class="ledger">
                <thead>
                    <tr>
                        <th>Kendaraan</th>
                        <th>Jenis</th>
                        <th>Pemilik</th>
                        <th>Masuk</th>
                        <th class="text-right">Lama Parkir</th>
                        <th class="text-right">Perkiraan Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parkir as $p)
                        <tr>
                            <td class="text-ink"><x-plate :value="$p->kendaraan->plat_nomor ?? null" /></td>
                            <td>{{ ucfirst($p->kendaraan->jenis_kendaraan ?? '-') }}</td>
                            <td>{{ $p->kendaraan->pemilik ?? '-' }}</td>
                            <td class="num text-slate-600">{{ $p->waktu_masuk->format('d M · H:i') }}</td>
                            <td class="num text-right">{{ $p->durasi_jam }} jam</td>
                            <td class="num text-right font-semibold">Rp {{ number_format($p->estimasi, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <p class="notice m-3">
                                    Belum ada kendaraan di area ini, jadi tidak ada yang bisa diproses keluar.
                                    Baris terisi saat petugas menerbitkan tiket untuk area ini di menu Masuk.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($canManage)
        <div class="sheet mt-6 flex flex-wrap items-center justify-between gap-3 p-4">
            <div>
                <p class="font-display text-sm font-bold uppercase tracking-wide text-primary-700">Panel petugas</p>
                <p class="num mt-0.5 text-xs text-slate-600">
                    {{ $reservasiMenunggu }} reservasi menunggu konfirmasi di area ini.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('ticket.area.edit', $area->id_area) }}" class="btn btn-quiet">Edit Area</a>
                <a href="{{ route('reservasi.index') }}" class="btn btn-primary">Reservasi Masuk</a>
            </div>
        </div>
    @endif
</div>
@endsection
