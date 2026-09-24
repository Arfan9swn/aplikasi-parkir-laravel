@extends('layouts.app')

@section('title', 'park.')
@section('page', 'tarif')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Edit Jenis Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-600">
                Ubah nama jenis dan/atau tarif per jamnya.
            </p>
        </div>
        <a href="{{ route('ticket.tarif') }}" class="btn btn-quiet">Batal</a>
    </div>

    @include('tarif.partials.form', ['item' => $item, 'inUse' => $inUse])
@endsection
