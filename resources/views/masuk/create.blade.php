@extends('layouts.app')

@section('title', 'park.')
@section('page', 'masuk')

@section('content')
    <div class="mx-auto max-w-4xl">
        <h1 class="font-display text-2xl font-bold text-ink">Kendaraan Masuk — Terbitkan Tiket</h1>
        <p class="mt-1 text-sm text-slate-600">Isi nomor polisi, pilih area parkir dan jenis kendaraannya.</p>

        <div class="mt-6 grid gap-4 lg:grid-cols-[minmax(0,1fr)_16rem]">
        <form method="POST" action="{{ url('/masuk') }}" autocomplete="off" class="sheet p-4 sm:p-5">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="masuk-plat" class="text-xs font-semibold text-slate-600">Nomor Polisi</label>
                    <input id="masuk-plat" name="plat_nomor" required type="text" placeholder="B 1234 ABC" value="{{ old('plat_nomor') }}"
                           class="field mt-1 font-mono text-base" />
                </div>
                <div>
                    <label for="masuk-jenis" class="text-xs font-semibold text-slate-600">Jenis Kendaraan</label>
                    <select id="masuk-jenis" name="jenis_kendaraan" required class="field mt-1 text-base">
                        <option value="">— Pilih jenis —</option>
                        @foreach ($tarifs as $t)
                            <option value="{{ $t->jenis_kendaraan }}" @selected(old('jenis_kendaraan') === $t->jenis_kendaraan)>{{ ucfirst($t->jenis_kendaraan) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="masuk-warna" class="text-xs font-semibold text-slate-600">Warna</label>
                    <input id="masuk-warna" name="warna" type="text" placeholder="Contoh: Hitam" value="{{ old('warna') }}"
                           class="field mt-1 text-base" />
                </div>
                <div>
                    <label for="masuk-pemilik" class="text-xs font-semibold text-slate-600">Nama Pemilik</label>
                    <input id="masuk-pemilik" name="pemilik" type="text" placeholder="Contoh: Budi" value="{{ old('pemilik') }}"
                           class="field mt-1 text-base" />
                </div>
                <div class="sm:col-span-2">
                    <label for="masuk-area" class="text-xs font-semibold text-slate-600">Area Parkir</label>
                    <select id="masuk-area" name="id_area" required class="field mt-1 text-base">
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

            <div class="mt-4 flex items-center justify-between border-t border-rule pt-3">
                <span class="text-xs text-slate-600">Petugas</span>
                <span class="text-xs font-semibold text-primary-700">Otomatis mengikuti area yang dipilih</span>
            </div>

            <button type="submit" class="btn btn-primary mt-4 w-full">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                    <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                </svg>
                Terbitkan Tiket
            </button>
        </form>

        {{-- What the gate needs while filling this form: the rate each vehicle
             type is billed at. The select above already carries the free-slot
             count, so this column carries the other half of the decision. --}}
        <aside class="sheet h-fit">
            <div class="sheet-head">
                <h2>Tarif per Jam</h2>
            </div>
            <ul>
                @forelse ($tarifs as $t)
                    <li class="clause items-baseline justify-between">
                        <span class="text-sm text-ink">{{ ucfirst($t->jenis_kendaraan) }}</span>
                        <span class="num text-sm font-semibold text-primary-700">Rp {{ number_format((float) $t->tarif_per_jam, 0, ',', '.') }}</span>
                    </li>
                @empty
                    <li class="p-3">
                        <p class="notice">Belum ada tarif, jadi tiket belum bisa diterbitkan. Tambahkan jenis dan tarifnya di menu Tarif.</p>
                    </li>
                @endforelse
            </ul>
        </aside>
        </div>
    </div>
@endsection
