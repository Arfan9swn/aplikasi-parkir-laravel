{{--
    Shared vehicle form used by both kendaraan/create and kendaraan/edit.

    Expects:
      $item   parkir_kendaraans model being edited, or null when creating
      $users  collection of parkir_users selectable as the data owner
      $tarifs collection of parkir_tarifs — the source of truth for the types
              a petugas may choose (add one on /tarif and it shows up here)
--}}
<form method="POST"
      action="{{ $item ? url('/kendaraan/' . $item->id_kendaraan) : url('/kendaraan') }}"
      class="sheet mt-6 p-4 sm:p-6">
    @csrf
    @if ($item)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="kendaraan-plat" class="block text-xs font-semibold text-slate-600">Nomor Polisi</label>
            <input id="kendaraan-plat" name="plat_nomor" type="text" required value="{{ old('plat_nomor', $item->plat_nomor ?? '') }}"
                   class="field mt-1 font-mono"
                   placeholder="cth: B 1234 ABC" />
            <p class="mt-1 text-xs text-slate-600">Tulis berurutan: kode area, nomor, lalu huruf belakang. Contoh: B 1234 ABC.</p>
            @error('plat_nomor')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kendaraan-jenis" class="block text-xs font-semibold text-slate-600">Jenis Kendaraan</label>
            <select id="kendaraan-jenis" name="jenis_kendaraan" required class="field mt-1">
                <option value="">— Pilih jenis —</option>
                @foreach ($tarifs as $t)
                    <option value="{{ $t->jenis_kendaraan }}"
                        @selected(old('jenis_kendaraan', $item->jenis_kendaraan ?? '') === $t->jenis_kendaraan)>
                        {{ ucfirst($t->jenis_kendaraan) }} — Rp {{ number_format((float) $t->tarif_per_jam, 0, ',', '.') }}/jam
                    </option>
                @endforeach
            </select>
            @if ($tarifs->isEmpty())
                <p class="notice mt-2 border-l-amber-500">
                    Belum ada jenis kendaraan. <a href="{{ route('ticket.tarif.create') }}" class="font-semibold underline">Tambah jenis</a> terlebih dahulu.
                </p>
            @endif
            @error('jenis_kendaraan')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kendaraan-warna" class="block text-xs font-semibold text-slate-600">Warna</label>
            <input id="kendaraan-warna" name="warna" type="text" value="{{ old('warna', $item->warna ?? '') }}"
                   class="field mt-1"
                   placeholder="cth: Hitam" />
        </div>

        <div class="sm:col-span-2">
            <label for="kendaraan-pemilik" class="block text-xs font-semibold text-slate-600">Nama Pemilik</label>
            <input id="kendaraan-pemilik" name="pemilik" type="text" value="{{ old('pemilik', $item->pemilik ?? '') }}"
                   class="field mt-1"
                   placeholder="cth: Budi Santoso" />
        </div>

        <div class="sm:col-span-2">
            <label for="kendaraan-petugas" class="block text-xs font-semibold text-slate-600">
                Petugas / Pemilik Data <span class="text-red-600">*</span>
            </label>
            <select id="kendaraan-petugas" name="id_user" required class="field mt-1">
                <option value="">— Pilih petugas —</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id_user }}"
                        @selected(old('id_user', $item->id_user ?? '') == $u->id_user)>
                        {{ $u->nama_lengkap }} — {{ ucfirst($u->role) }} ({{ $u->username }})
                    </option>
                @endforeach
            </select>
            @error('id_user')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-2 border-t border-rule pt-4">
        <a href="{{ route('ticket.kendaraan') }}" class="btn btn-quiet">Batal</a>
        <button type="submit" class="btn btn-primary">
            {{ $item ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
</form>