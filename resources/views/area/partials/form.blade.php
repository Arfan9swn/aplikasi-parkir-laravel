{{--
    Shared area form used by both area/create and area/edit.

    Expects:
      $item            parkir_areas model being edited, or null when creating
      $petugasOptions  collection of parkir_users eligible to be area petugas
--}}
<form method="POST"
      action="{{ $item ? url('/area/' . $item->id_area) : url('/area') }}"
      class="sheet mt-6 p-4 sm:p-6">
    @csrf
    @if ($item)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="area-nama" class="block text-xs font-semibold text-slate-600">Nama Area</label>
            <input id="area-nama" name="nama_area" type="text" required value="{{ old('nama_area', $item->nama_area ?? '') }}"
                   class="field mt-1"
                   placeholder="cth: Area B" />
            @error('nama_area')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="area-kapasitas" class="block text-xs font-semibold text-slate-600">Kapasitas</label>
            <input id="area-kapasitas" name="kapasitas" type="number" min="1" required value="{{ old('kapasitas', $item->kapasitas ?? '') }}"
                   class="field mt-1"
                   placeholder="10" />
            @error('kapasitas')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="area-terisi" class="block text-xs font-semibold text-slate-600">Terisi</label>
            <input id="area-terisi" name="terisi" type="number" min="0" required value="{{ old('terisi', $item->terisi ?? '0') }}"
                   class="field mt-1"
                   placeholder="0" />
            @error('terisi')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="area-petugas" class="block text-xs font-semibold text-slate-600">
                Petugas Area <span class="text-red-600">*</span>
            </label>
            <select id="area-petugas" name="id_user" required class="field mt-1">
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
            <p class="mt-1 text-xs text-slate-600">
                Setiap area membutuhkan petugas yang berbeda. Petugas yang sudah ditugaskan ke area lain tidak ditampilkan di sini.
                Pada mode edit, petugas saat ini area tetap tersedia.
            </p>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-2 border-t border-rule pt-4">
        <a href="{{ route('ticket.area') }}" class="btn btn-quiet">Batal</a>
        <button type="submit" class="btn btn-primary">
            {{ $item ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
</form>