@extends('layouts.app')

@section('title', 'park.')
@section('page', 'tarif')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Jenis Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-500">
                Ubah nama jenis dan/atau tarif per jamnya.
            </p>
        </div>
        <a href="{{ route('ticket.tarif') }}"
           class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
            Batal
        </a>
    </div>

    @include('tarif.partials.form', ['item' => $item, 'inUse' => $inUse])
@endsection
