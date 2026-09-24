@extends('layouts.app')

@section('title', 'park.')
@section('page', 'kendaraan')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Edit Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-600">
                Perbarui data kendaraan ini.
            </p>
        </div>
        <a href="{{ route('ticket.kendaraan') }}" class="btn btn-quiet">Batal</a>
    </div>

    @include('kendaraan.partials.form', ['item' => $vehicle, 'users' => $users])
@endsection