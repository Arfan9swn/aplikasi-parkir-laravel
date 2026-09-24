@extends('layouts.app')

@section('title', 'park.')
@section('page', 'transaksi')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Data Tiket</h1>
            <p class="mt-1 text-sm text-slate-600">Semua tiket yang pernah terbit — aktif maupun selesai.</p>
        </div>
        <form method="GET" action="{{ url('/transaksi') }}" class="flex flex-wrap items-center gap-3">
            <input name="q" type="text" value="{{ $q }}" placeholder="Cari plat atau nomor tiket…" class="field w-60" />
            <div class="flex items-center gap-4 text-xs">
                <span class="text-slate-600">Filter:</span>
                <x-sort-tabs field="status" :current="$status" :tabs="[
                    ['value' => 'all', 'label' => 'Semua', 'description' => 'Semua tiket, aktif maupun selesai'],
                    ['value' => 'masuk', 'label' => 'Aktif', 'description' => 'Kendaraan yang sedang parkir'],
                    ['value' => 'keluar', 'label' => 'Selesai', 'description' => 'Tiket yang sudah lunas'],
                ]" />
            </div>
            <button type="submit" data-tooltip="Jalankan pencarian dan filter" class="btn btn-primary">Terapkan</button>
        </form>
    </div>

    <p class="num mt-4 text-xs text-slate-600">{{ $tickets->count() }} tiket ditampilkan</p>

    <div class="sheet mt-3 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="ledger">
                <thead>
                    <tr>
                        <th>Tiket</th>
                        <x-table-sort column="plat" label="Plat" :allowed="$allowed" />
                        <th>Area</th>
                        <th>Petugas</th>
                        <x-table-sort column="waktu_masuk" label="Masuk" :allowed="$allowed" />
                        <x-table-sort column="waktu_keluar" label="Keluar" :allowed="$allowed" />
                        <x-table-sort column="durasi_jam" label="Durasi" :allowed="$allowed" />
                        <x-table-sort column="biaya_total" label="Biaya" :allowed="$allowed" />
                        <x-table-sort column="status" label="Status" :allowed="$allowed" />
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $t)
                        <tr>
                            <td class="font-mono font-semibold text-primary-700">P-{{ str_pad($t->id_parkir, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="font-mono text-ink">{{ $t->kendaraan->plat_nomor ?? '-' }}</td>
                            <td>{{ $t->area->nama_area ?? '-' }}</td>
                            <td class="text-slate-600">{{ $t->user->nama_lengkap ?? '-' }}</td>
                            <td class="num text-slate-600">{{ $t->waktu_masuk ? \Carbon\Carbon::parse($t->waktu_masuk)->format('d M · H:i') : '-' }}</td>
                            <td class="num text-slate-600">{{ $t->waktu_keluar ? \Carbon\Carbon::parse($t->waktu_keluar)->format('d M · H:i') : '-' }}</td>
                            <td class="num text-right">{{ $t->durasi_jam ?? ($t->status === 'masuk' ? '—' : '-') }}</td>
                            <td class="num text-right font-semibold">{{ $t->biaya_total ? 'Rp ' . number_format($t->biaya_total, 0, ',', '.') : '—' }}</td>
                            <td>
                                @if ($t->status === 'masuk')
                                    <span class="state bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="state bg-slate-200 text-slate-700">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-0">
                                <p class="notice m-3">
                                    @if ($q !== '' || $status !== 'all')
                                        Tidak ada tiket yang cocok dengan pencarian atau filter ini. Hapus kata kunci, atau pilih Semua untuk melihat seluruh tiket.
                                    @else
                                        Belum ada tiket yang terbit. Tiket pertama muncul setelah petugas menerbitkan tiket di menu Masuk.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
