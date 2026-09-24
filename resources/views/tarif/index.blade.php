@extends('layouts.app')

@section('title', 'park.')
@section('page', 'tarif')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Jenis Kendaraan &amp; Tarif</h1>
            <p class="mt-1 text-sm text-slate-600">
                Setiap jenis di sini menjadi pilihan jenis kendaraan di gerbang masuk.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ url('/tarif') }}" class="flex items-center gap-2">
                <input name="q" type="text" value="{{ $q }}" placeholder="Cari jenis kendaraan…" class="field w-56" />
                <button type="submit" class="btn btn-quiet">Cari</button>
            </form>
            @if ($canManage)
                <a href="{{ route('ticket.tarif.create') }}" class="btn btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Jenis
                </a>
            @endif
        </div>
    </div>

    <div class="sheet overflow-hidden">
        <div class="overflow-x-auto">
            <table class="ledger">
                <thead>
                    <tr>
                        <x-table-sort column="jenis_kendaraan" label="Jenis" :allowed="$allowed" />
                        <x-table-sort column="tarif_per_jam" label="Tarif / Jam" :allowed="$allowed" />
                        <x-table-sort column="vehicles" label="Kendaraan" :allowed="$allowed" />
                        <th class="text-right">Sedang Parkir</th>
                        <x-table-sort column="tickets" label="Total Tiket" :allowed="$allowed" />
                        <x-table-sort column="revenue" label="Pendapatan" :allowed="$allowed" />
                        @if ($canManage)
                            <th class="text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tarifs as $t)
                        @php $u = $usage[$t->id_tarif]; @endphp
                        <tr>
                            <td class="font-semibold text-ink">{{ ucfirst($t->jenis_kendaraan) }}</td>
                            <td class="num text-right">Rp {{ number_format((float) $t->tarif_per_jam, 0, ',', '.') }}</td>
                            <td class="num text-right text-slate-600">{{ $u['vehicles'] }}</td>
                            <td class="text-right">
                                @if ($u['parked'] > 0)
                                    <span class="state bg-emerald-100 text-emerald-800">{{ $u['parked'] }} di tempat</span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="num text-right text-slate-600">{{ $u['tickets'] }}</td>
                            <td class="num text-right font-semibold">Rp {{ number_format($u['revenue'], 0, ',', '.') }}</td>
                            @if ($canManage)
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('ticket.tarif.edit', $t->id_tarif) }}" class="btn btn-quiet">Edit</a>
                                        <form method="POST" action="{{ url('/tarif/' . $t->id_tarif) }}">
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
                            <td colspan="{{ $canManage ? 7 : 6 }}" class="p-0">
                                <p class="notice m-3">
                                    @if ($q !== '')
                                        Tidak ada jenis kendaraan yang cocok dengan kata kunci ini. Coba kata kunci lain, atau kosongkan pencarian.
                                    @elseif ($canManage)
                                        Belum ada jenis kendaraan, jadi gerbang Masuk belum punya pilihan jenis. Tambahkan satu lewat tombol Tambah Jenis.
                                    @else
                                        Belum ada jenis kendaraan yang terdaftar. Data muncul setelah petugas menambahkannya.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="mt-4 text-xs text-slate-600">
        Hapus hanya bisa dilakukan bila jenis tersebut belum dipakai kendaraan maupun tiket mana pun.
    </p>
</div>
@endsection