@extends('layouts.app')

@section('title', 'park.')
@section('page', 'reservasi')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="font-display text-2xl font-bold text-ink">Reservasi Slot Parkir</h1>
        <p class="mt-1 text-sm text-slate-600">
            Tidak perlu login — ajukan slot di area pilihan Anda, petugas akan mengonfirmasi ketersediaannya.
        </p>

        <div class="sheet mt-6">
            <div class="sheet-head">
                <h2>Isi Data Reservasi</h2>
            </div>
            <form method="POST" action="{{ route('reservasi.store') }}" autocomplete="off" class="p-4 sm:p-6">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="reservasi-area" class="text-xs font-semibold text-slate-600">Area Parkir <span class="text-red-600">*</span></label>
                    <select id="reservasi-area" name="id_area" required class="field mt-1 text-base">
                        <option value="">— Pilih area —</option>
                        @foreach ($areas as $a)
                            @php $free = max(0, $a->kapasitas - $a->terisi); @endphp
                            <option value="{{ $a->id_area }}"
                                @selected((string) old('id_area', $selectedId) === (string) $a->id_area)
                                @disabled($a->kapasitas > 0 && $a->terisi >= $a->kapasitas)>
                                {{ $a->nama_area }} — {{ $free > 0 ? $free . ' slot kosong' : 'PENUH' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_area')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reservasi-plat" class="text-xs font-semibold text-slate-600">Nomor Polisi <span class="text-red-600">*</span></label>
                    <input id="reservasi-plat" name="plat_nomor" required type="text" maxlength="20" placeholder="B 1234 ABC"
                           value="{{ old('plat_nomor') }}"
                           class="field mt-1 font-mono" />
                    @error('plat_nomor')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reservasi-waktu" class="text-xs font-semibold text-slate-600">Perkiraan Waktu Datang <span class="text-red-600">*</span></label>
                    <input id="reservasi-waktu" name="waktu_datang" required type="datetime-local" value="{{ old('waktu_datang') }}"
                           class="field num mt-1" />
                    @error('waktu_datang')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reservasi-pemilik" class="text-xs font-semibold text-slate-600">Nama Pemilik</label>
                    <input id="reservasi-pemilik" name="pemilik" type="text" maxlength="100" placeholder="Contoh: Budi"
                           value="{{ old('pemilik') }}"
                           class="field mt-1" />
                </div>

                <div>
                    <label for="reservasi-kontak" class="text-xs font-semibold text-slate-600">Kontak (WA / telp)</label>
                    <input id="reservasi-kontak" name="kontak" type="text" maxlength="100" placeholder="Contoh: 0812xxxx"
                           value="{{ old('kontak') }}"
                           class="field mt-1" />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2 border-t border-rule pt-4">
                <a href="{{ url('/area') }}" class="btn btn-quiet">Lihat area dulu</a>
                <button type="submit" class="btn btn-primary">Kirim Reservasi</button>
            </div>
        </form>
        </div>
    </div>
@endsection
