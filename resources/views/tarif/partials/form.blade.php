{{--
    Shared vehicle-type form used by both tarif/create and tarif/edit.

    Expects:
      $item   parkir_tarifs model being edited, or null when creating
      $inUse  how many vehicles currently use this type (edit only)
--}}
<form method="POST"
      action="{{ $item ? url('/tarif/' . $item->id_tarif) : url('/tarif') }}"
      class="sheet mt-6 p-4 sm:p-6">
    @csrf
    @if ($item)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="tarif-jenis" class="block text-xs font-semibold text-slate-600">Nama Jenis Kendaraan</label>
            <input id="tarif-jenis" name="jenis_kendaraan" type="text" required maxlength="50"
                   value="{{ old('jenis_kendaraan', $item->jenis_kendaraan ?? '') }}"
                   class="field mt-1"
                   placeholder="cth: Truk" />
            @error('jenis_kendaraan')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tarif-per-jam" class="block text-xs font-semibold text-slate-600">Tarif per Jam (Rp)</label>
            <input id="tarif-per-jam" name="tarif_per_jam" type="number" required min="0" step="100"
                   value="{{ old('tarif_per_jam', $item->tarif_per_jam ?? '') }}"
                   class="field num mt-1"
                   placeholder="cth: 3000" />
            @error('tarif_per_jam')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if ($item && $inUse > 0)
        <p class="notice mt-4 border-l-amber-500">
            {{ $inUse }} kendaraan memakai jenis ini. Mengubah namanya akan memindahkan
            kendaraan tersebut ke jenis baru secara otomatis.
        </p>
    @endif

    <div class="mt-6 flex items-center justify-end gap-2 border-t border-rule pt-4">
        <a href="{{ route('ticket.tarif') }}" class="btn btn-quiet">Batal</a>
        <button type="submit" class="btn btn-primary">
            {{ $item ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
</form>
