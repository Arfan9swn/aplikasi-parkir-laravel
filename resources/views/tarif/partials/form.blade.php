{{--
    Shared vehicle-type form used by both tarif/create and tarif/edit.

    Expects:
      $item   parkir_tarifs model being edited, or null when creating
      $inUse  how many vehicles currently use this type (edit only)
--}}
<form method="POST"
      action="{{ $item ? url('/tarif/' . $item->id_tarif) : url('/tarif') }}"
      class="mt-6 rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8">
    @csrf
    @if ($item)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-slate-500">Nama Jenis Kendaraan</label>
            <input name="jenis_kendaraan" type="text" required maxlength="50"
                   value="{{ old('jenis_kendaraan', $item->jenis_kendaraan ?? '') }}"
                   class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                   placeholder="cth: Truk" />
            @error('jenis_kendaraan')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500">Tarif per Jam (Rp)</label>
            <input name="tarif_per_jam" type="number" required min="0" step="100"
                   value="{{ old('tarif_per_jam', $item->tarif_per_jam ?? '') }}"
                   class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-sm font-mono text-slate-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                   placeholder="cth: 3000" />
            @error('tarif_per_jam')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if ($item && $inUse > 0)
        <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
            {{ $inUse }} kendaraan memakai jenis ini. Mengubah namanya akan memindahkan
            kendaraan tersebut ke jenis baru secara otomatis.
        </p>
    @endif

    <div class="mt-6 flex items-center justify-end gap-3 border-t border-primary-100 pt-4">
        <a href="{{ route('ticket.tarif') }}"
           class="rounded-xl border border-primary-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
            Batal
        </a>
        <button type="submit"
                class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
            {{ $item ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
</form>
