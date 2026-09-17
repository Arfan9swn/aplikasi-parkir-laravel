@extends('layouts.app')

@section('title', 'park.')
@section('page', 'area')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Area Parkir</h1>
            <p class="mt-1 text-sm text-slate-500">
                Buat area parkir baru. Setiap area wajib memiliki satu petugas khusus yang belum ditugaskan ke area lain.
            </p>
        </div>
        <a href="{{ route('ticket.area') }}"
           class="rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-primary-50">
            Batal
        </a>
    </div>

    @include('area.partials.form', ['item' => $area, 'petugasOptions' => $petugasOptions])
@endsection