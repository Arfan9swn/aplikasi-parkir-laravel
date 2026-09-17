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
      class="mt-6 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8">
    @csrf
    @if ($item)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-500">Nomor Polisi</label>
            <input name="plat_nomor" type="text" required value="{{ old('plat_nomor', $item->plat_nomor ?? '') }}"
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
                @foreach ($tarifs as $t)
                    <option value="{{ $t->jenis_kendaraan }}"
                        @selected(old('jenis_kendaraan', $item->jenis_kendaraan ?? '') === $t->jenis_kendaraan)>
                        {{ ucfirst($t->jenis_kendaraan) }} — Rp {{ number_format((float) $t->tarif_per_jam, 0, ',', '.') }}/jam
                    </option>
                @endforeach
            </select>
            @if ($tarifs->isEmpty())
                <p class="mt-1 text-xs font-medium text-amber-600">
                    Belum ada jenis kendaraan. <a href="{{ route('ticket.tarif.create') }}" class="underline">Tambah jenis</a> terlebih dahulu.
                </p>
            @endif
            @error('jenis_kendaraan')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500">Warna</label>
            <input name="warna" type="text" value="{{ old('warna', $item->warna ?? '') }}"
                   class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                   placeholder="cth: Hitam" />
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-500">Nama Pemilik</label>
            <input name="pemilik" type="text" value="{{ old('pemilik', $item->pemilik ?? '') }}"
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

    <div class="mt-6 flex items-center justify-end gap-3 border-t border-primary-100 pt-4">
        <a href="{{ route('ticket.kendaraan') }}"
           class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
            Batal
        </a>
        <button type="submit"
                class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
            {{ $item ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
</form>