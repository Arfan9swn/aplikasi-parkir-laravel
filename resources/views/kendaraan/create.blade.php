@extends('layouts.app')

@section('title', 'park.')
@section('page', 'kendaraan')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-500">
                Daftarkan kendaraan baru.
            </p>
        </div>
        <a href="{{ route('ticket.kendaraan') }}"
           class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
            Batal
        </a>
    </div>

    @include('kendaraan.partials.form', ['item' => $vehicle, 'users' => $users])
@endsection