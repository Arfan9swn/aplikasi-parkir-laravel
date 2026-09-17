@extends('layouts.app')

@section('title', 'park.')
@section('page', 'kendaraan')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $vehicle ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $vehicle ? 'Perbarui data kendaraan ini.' : 'Daftarkan kendaraan baru.' }}
            </p>
        </div>
        <a href="{{ route('ticket.kendaraan') }}"
           class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
            Batal
        </a>
    </div>

    <form method="POST"
          action="{{ $vehicle ? url('/kendaraan/' . $vehicle->id_kendaraan) : url('/kendaraan') }}"
          class="mt-6 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8">
        @csrf
        @if ($vehicle)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500">Nomor Polisi</label>
                <input name="plat_nomor" type="text" required value="{{ old('plat_nomor', $vehicle->plat_nomor ?? '') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm font-mono text-slate-800 uppercase focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                       placeholder="cth: B 1234 ABC" />
                @error('plat_nomor')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Jenis Kendaraan</label>
                <select name="jenis_kendaraan" required
                        class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                    <option value="">— Pilih jenis —</option>
                    @foreach (['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'] as $val => $lbl)
                        <option value="{{ $val }}"
                            @selected(old('jenis_kendaraan', $vehicle->jenis_kendaraan ?? '') === $val)>
                            {{ $lbl }}
                        </option>
                    @endforeach
                </select>
                @error('jenis_kendaraan')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Warna</label>
                <input name="warna" type="text" value="{{ old('warna', $vehicle->warna ?? '') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                       placeholder="cth: Hitam" />
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500">Nama Pemilik</label>
                <input name="pemilik" type="text" value="{{ old('pemilik', $vehicle->pemilik ?? '') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                       placeholder="cth: Budi Santoso" />
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500">
                    Petugas / Pemilik Data <span class="text-red-500">*</span>
                </label>
                <select name="id_user" required
                        class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                    <option value="">— Pilih petugas —</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id_user }}"
                            @selected(old('id_user', $vehicle->id_user ?? '') == $u->id_user)>
                            {{ $u->nama_lengkap }} — {{ ucfirst($u->role) }} ({{ $u->username }})
                        </option>
                    @endforeach
                </select>
                @error('id_user')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-primary-100 pt-4">
            <a href="{{ route('ticket.kendaraan') }}"
               class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
                {{ $vehicle ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>
    </form>
@endsection