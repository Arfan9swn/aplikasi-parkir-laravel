@extends('layouts.app')

@section('title', 'park.')
@section('page', 'masuk')

@section('content')
    <div class="mx-auto max-w-3xl">
        @unless (isset($ticket) && $ticket)
            <h1 class="text-2xl font-bold text-slate-800">Kendaraan Masuk — Terbitkan Tiket</h1>
            <p class="mt-1 text-sm text-slate-500">Isi nomor polisi, pilih area parkir dan jenis kendaraannya.</p>

            <form method="POST" action="{{ url('/masuk') }}" autocomplete="off" class="mt-6 space-y-5">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-slate-500">Nomor Polisi</label>
                        <input name="plat_nomor" required type="text" placeholder="B 1234 ABC" value="{{ old('plat_nomor') }}"
                               class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500">Jenis Kendaraan</label>
                        <select name="jenis_kendaraan" required
                                class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                            <option value="">— Pilih jenis —</option>
                            @foreach ($tarifs as $t)
                                <option value="{{ $t->jenis_kendaraan }}" @selected(old('jenis_kendaraan') === $t->jenis_kendaraan)>{{ ucfirst($t->jenis_kendaraan) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500">Warna</label>
                        <input name="warna" type="text" placeholder="Contoh: Hitam" value="{{ old('warna') }}"
                               class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500">Nama Pemilik</label>
                        <input name="pemilik" type="text" placeholder="Contoh: Budi" value="{{ old('pemilik') }}"
                               class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-slate-500">Area Parkir</label>
                        <select name="id_area" required
                                class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                            <option value="">— Pilih area —</option>
                            @foreach ($areas as $a)
                                @php $free = $a->kapasitas - $a->terisi; @endphp
                                <option value="{{ $a->id_area }}" @selected((string) old('id_area') === (string) $a->id_area)>
                                    {{ $a->nama_area }} — {{ max(0, $free) }} slot kosong{{ $free > 0 ? '' : ' (PENUH)' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-xl border border-primary-200/60 bg-primary-50 px-4 py-2.5">
                    <span class="text-xs text-slate-500">Petugas</span>
                    <span class="text-xs font-medium text-primary-700">Otomatis mengikuti area yang dipilih</span>
                </div>

                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary-600">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                        <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                    Terbitkan Tiket
                </button>
            </form>
        @endunless
@isset($ticket)
            <div class="print-area">
                <h1 class="text-2xl font-bold text-slate-800">Tiket Berhasil Terbit</h1>
                <div class="mx-auto mt-6 max-w-md rounded-2xl border-2 border-dashed border-primary-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold tracking-widest text-slate-400">TIKET PARKIR</span>
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-primary-500 text-xs font-bold text-white">P</span>
                    </div>

                    <div class="mt-2 text-center">
                        <p class="text-xs font-semibold tracking-widest text-slate-400">NOMOR TIKET</p>
                        <p class="font-mono text-3xl font-extrabold tracking-widest text-primary-700">P-{{ str_pad($ticket->id_parkir, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <p class="text-xs text-slate-400">Plat</p>
                            <p class="font-mono font-semibold text-slate-800">{{ $ticket->kendaraan->plat_nomor }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Jenis</p>
                            <p class="font-semibold text-slate-800">{{ ucfirst($ticket->kendaraan->jenis_kendaraan) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Warna</p>
                            <p class="text-slate-800">{{ $ticket->kendaraan->warna }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Pemilik</p>
                            <p class="text-slate-800">{{ $ticket->kendaraan->pemilik }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-slate-400">Area</p>
                            <p class="font-semibold text-slate-800">{{ $ticket->area->nama_area }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-slate-400">Waktu Masuk</p>
                            <p class="font-mono font-semibold text-slate-800">{{ \Carbon\Carbon::parse($ticket->waktu_masuk)->format('d M Y · H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-3 border-t border-primary-100 pt-3">
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-primary-700 px-4 py-2 text-xs font-bold text-white transition hover:bg-primary-800">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/>
                            </svg>
                            Cetak Tiket
                        </button>
                        <a href="{{ url('/masuk') }}"
                           class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">
                            + Kendaraan Baru
                        </a>
                    </div>
                </div>
            </div>
        @endisset
    </div>
@endsection