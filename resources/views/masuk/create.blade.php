@extends('layouts.app')

@section('title', 'park.')
@section('page', 'masuk')

@section('content')
    <div class="mx-auto max-w-3xl">
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
    </div>
@endsection
