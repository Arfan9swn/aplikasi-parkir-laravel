@extends('layouts.app')

@section('title', 'park.')
@section('page', 'area')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Area Parkir</h1>
            <p class="mt-1 text-sm text-slate-600">Kepadatan parkir per area — setiap area memiliki petugas khusus.</p>
        </div>
        @if ($canManage)
            <a href="{{ route('ticket.area.create') }}" class="btn btn-primary">
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
    <div class="sheet mb-6">
        <div class="cols-ruled">
            <div>
                <p class="figure-label">Total Slot</p>
                <p class="figure mt-1">{{ number_format((int) $totalSpots, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Kapasitas gabungan</p>
            </div>
            <div>
                <p class="figure-label">Terisi</p>
                <p class="figure mt-1">{{ number_format((int) $totalUsed, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">{{ $totalSpots > 0 ? (int) min(100, ($totalUsed / $totalSpots) * 100) : 0 }}% padatnya</p>
            </div>
            <div>
                <p class="figure-label">Kosong</p>
                <p class="figure mt-1">{{ number_format((int) $totalFree, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Slot tersedia</p>
            </div>
            <div>
                <p class="figure-label">Area Terdaftar</p>
                <p class="figure mt-1">{{ $areas->count() }}</p>
                <p class="figure-note mt-0.5">Area aktif</p>
            </div>
        </div>
    </div>

    <div class="sheet overflow-hidden">
        <div class="overflow-x-auto">
            <table class="ledger">
                <thead>
                    <tr>
                        <x-table-sort column="nama_area" label="Area" :allowed="$allowed" />
                        <x-table-sort column="petugas" label="Petugas" :allowed="$allowed" />
                        <x-table-sort column="kapasitas" label="Kapasitas" :allowed="$allowed" />
                        <x-table-sort column="terisi" label="Terisi / Kosong" :allowed="$allowed" />
                        <th>Status</th>
                        @if ($canManage)
                            <th class="text-right">Aksi</th>
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
                        <tr>
                            <td>
                                <a href="{{ route('ticket.area.show', $a->id_area) }}"
                                   class="font-semibold text-ink transition hover:text-primary-700">{{ $a->nama_area }}</a>
                                <p class="mt-0.5 text-[11px] text-slate-600">Lihat kendaraan di area ini →</p>
                            </td>
                            <td class="text-slate-600">{{ $a->petugas->nama_lengkap ?? '-' }}</td>
                            <td class="num">{{ $a->kapasitas }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-20 bg-primary-100">
                                        <div class="h-full {{ $full ? 'bg-red-500' : 'bg-primary-500' }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="num text-xs text-slate-600">{{ $a->terisi }}/{{ $a->kapasitas }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($a->id_user)
                                    <span class="state bg-emerald-100 text-emerald-800">Teredaftar</span>
                                @else
                                    <span class="state bg-amber-100 text-amber-800">Tanpa petugas</span>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('ticket.area.edit', $a->id_area) }}" class="btn btn-quiet">Edit</a>
                                        <form method="POST" action="{{ url('/area/' . $a->id_area) }}">
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
                            <td colspan="6" class="p-0">
                                <p class="notice m-3">
                                    Belum ada area parkir, jadi belum ada slot yang bisa diisi.
                                    @if ($canManage)
                                        Tambahkan area pertama lewat tombol Tambah Area di atas.
                                    @else
                                        Data muncul setelah petugas mendaftarkan area.
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
