@extends('layouts.app')

@section('title', 'park.')
@section('page', 'keluar')

@section('content')
<div class="mx-auto max-w-3xl">
    <h1 class="text-2xl font-bold text-slate-800">Kendaraan Keluar — Pembayaran</h1>
    <p class="mt-1 text-sm text-slate-500">Masukkan nomor polisi kendaraan yang akan keluar.</p>
    <form method="POST" action="{{ url('/keluar/check') }}" autocomplete="off" class="mt-6">
        @csrf
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label for="keluar-plate" class="text-xs font-medium text-slate-500">Nomor Polisi</label>
                <input id="keluar-plate" name="plat_nomor" required type="text" placeholder="B 1234 ABC"
                       value="{{ old('plat_nomor') }}"
                       class="mt-1 block w-full rounded-xl border border-primary-200 px-4 py-2.5 text-base text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            </div>
            <button type="submit"
                class="rounded-xl bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">
                Cari Tiket
            </button>
        </div>
    </form>
</div>
@endsection
