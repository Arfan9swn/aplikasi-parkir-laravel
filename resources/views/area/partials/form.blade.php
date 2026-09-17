{{--
    Shared area form used by both area/create and area/edit.

    Expects:
      $item            parkir_areas model being edited, or null when creating
      $petugasOptions  collection of parkir_users eligible to be area petugas
--}}
<form method="POST"
      action="{{ $item ? url('/area/' . $item->id_area) : url('/area') }}"
      class="mt-6 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8">
    @csrf
    @if ($item)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-500">Nama Area</label>
            <input name="nama_area" type="text" required value="{{ old('nama_area', $item->nama_area ?? '') }}"
                   class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                   placeholder="cth: Area B" />
            @error('nama_area')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500">Kapasitas</label>
            <input name="kapasitas" type="number" min="1" required value="{{ old('kapasitas', $item->kapasitas ?? '') }}"
                   class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                   placeholder="10" />
            @error('kapasitas')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500">Terisi</label>
            <input name="terisi" type="number" min="0" required value="{{ old('terisi', $item->terisi ?? '0') }}"
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
                        @selected(old('id_user', $item->id_user ?? '') == $p->id_user)>
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
            {{ $item ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
</form>