@extends('layouts.app')

@section('title', 'park.')
@section('page', 'area')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $area ? 'Edit Area Parkir' : 'Tambah Area Parkir' }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $area ? 'Perbarui detail area parkir ini.' : 'Buat area parkir baru. Setiap area wajib memiliki satu petugas khusus yang belum ditugaskan ke area lain.' }}
            </p>
        </div>
        <a href="{{ route('ticket.area') }}"
           class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
            Batal
        </a>
    </div>

    <form method="POST"
          action="{{ $area ? url('/area/' . $area->id_area) : url('/area') }}"
          class="mt-6 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8">
        @csrf
        @if ($area)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500">Nama Area</label>
                <input name="nama_area" type="text" required value="{{ old('nama_area', $area->nama_area ?? '') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                       placeholder="cth: Area B" />
                @error('nama_area')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Kapasitas</label>
                <input name="kapasitas" type="number" min="1" required value="{{ old('kapasitas', $area->kapasitas ?? '') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                       placeholder="10" />
                @error('kapasitas')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Terisi</label>
                <input name="terisi" type="number" min="0" required value="{{ old('terisi', $area->terisi ?? '0') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                       placeholder="0" />
                @error('terisi')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500">
                    Petugas Area <span class="text-red-500">*</span>
                </label>
                <select name="id_user" required
                        class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                    <option value="">— Pilih petugas —</option>
                    @foreach ($petugasOptions as $p)
                        <option value="{{ $p->id_user }}"
                            @selected(old('id_user', $area->id_user ?? '') == $p->id_user)>
                            {{ $p->nama_lengkap }} — {{ ucfirst($p->role) }} ({{ $p->username }})
                        </option>
                    @endforeach
                </select>
                @error('id_user')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-400">
                    Setiap area membutuhkan petugas yang berbeda. Petugas yang sudah ditugaskan ke area lain tidak ditampilkan di sini.
                    Pada mode edit, petugas saat ini area tetap tersedia.
                </p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-primary-100 pt-4">
            <a href="{{ route('ticket.area') }}"
               class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
                Batal
            </a>
            <button type="submit"
                    class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
                {{ $area ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>
    </form>
@endsection
