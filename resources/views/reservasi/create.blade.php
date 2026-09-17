@extends('layouts.app')

@section('title', 'park.')
@section('page', 'reservasi')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold text-slate-800">Reservasi Slot Parkir</h1>
        <p class="mt-1 text-sm text-slate-500">
            Tidak perlu login — ajukan slot di area pilihan Anda, petugas akan mengonfirmasi ketersediaannya.
        </p>

        <form method="POST" action="{{ route('reservasi.store') }}" autocomplete="off"
              class="mt-6 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Area Parkir <span class="text-red-500">*</span></label>
                    <select name="id_area" required
                            class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
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
                    <label class="text-xs font-medium text-slate-500">Nomor Polisi <span class="text-red-500">*</span></label>
                    <input name="plat_nomor" required type="text" maxlength="20" placeholder="B 1234 ABC"
                           value="{{ old('plat_nomor') }}"
                           class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm font-mono uppercase text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                    @error('plat_nomor')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500">Perkiraan Waktu Datang <span class="text-red-500">*</span></label>
                    <input name="waktu_datang" required type="datetime-local" value="{{ old('waktu_datang') }}"
                           class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                    @error('waktu_datang')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500">Nama Pemilik</label>
                    <input name="pemilik" type="text" maxlength="100" placeholder="Contoh: Budi"
                           value="{{ old('pemilik') }}"
                           class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500">Kontak (WA / telp)</label>
                    <input name="kontak" type="text" maxlength="100" placeholder="Contoh: 0812xxxx"
                           value="{{ old('kontak') }}"
                           class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-primary-100 pt-4">
                <a href="{{ url('/area') }}"
                   class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                    Lihat area dulu
                </a>
                <button type="submit"
                        class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
                    Kirim Reservasi
                </button>
            </div>
        </form>
    </div>
@endsection
