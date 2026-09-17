@extends('layouts.app')

@section('title', 'park.')
@section('page', 'tarif')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Jenis Kendaraan &amp; Tarif</h1>
            <p class="mt-1 text-sm text-slate-500">
                Setiap jenis di sini menjadi pilihan jenis kendaraan di gerbang masuk.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ url('/tarif') }}" class="flex items-center gap-2">
                <input name="q" type="text" value="{{ $q }}" placeholder="Cari jenis kendaraan…"
                       class="w-56 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                <button type="submit"
                        class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                    Cari
                </button>
            </form>
            @if ($canManage)
                <a href="{{ route('ticket.tarif.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Jenis
                </a>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3 font-semibold">Jenis</th>
                        <th class="px-4 py-3 font-semibold">Tarif / Jam</th>
                        <th class="px-4 py-3 font-semibold">Kendaraan</th>
                        <th class="px-4 py-3 font-semibold">Sedang Parkir</th>
                        <th class="px-4 py-3 font-semibold">Total Tiket</th>
                        <th class="px-4 py-3 font-semibold">Pendapatan</th>
                        @if ($canManage)
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tarifs as $t)
                        @php $u = $usage[$t->id_tarif]; @endphp
                        <tr class="border-b border-primary-100 last:border-0">
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ ucfirst($t->jenis_kendaraan) }}</td>
                            <td class="px-4 py-3 font-mono">Rp {{ number_format((float) $t->tarif_per_jam, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $u['vehicles'] }}</td>
                            <td class="px-4 py-3">
                                @if ($u['parked'] > 0)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">{{ $u['parked'] }}</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $u['tickets'] }}</td>
                            <td class="px-4 py-3 font-mono">Rp {{ number_format($u['revenue'], 0, ',', '.') }}</td>
                            @if ($canManage)
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('ticket.tarif.edit', $t->id_tarif) }}"
                                           class="rounded-lg border border-primary-200 bg-white px-2.5 py-1 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ url('/tarif/' . $t->id_tarif) }}">
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
                            <td colspan="{{ $canManage ? 7 : 6 }}" class="px-4 py-10 text-center text-sm text-slate-300">
                                {{ $q !== '' ? 'Tidak ada jenis kendaraan yang cocok.' : 'Belum ada jenis kendaraan.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="mt-4 text-xs text-slate-400">
        Hapus hanya bisa dilakukan bila jenis tersebut belum dipakai kendaraan maupun tiket mana pun.
    </p>
</div>
@endsection