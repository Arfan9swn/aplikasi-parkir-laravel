@extends('layouts.app')

@section('title', 'park.')
@section('page', 'area')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Edit Area Parkir</h1>
            <p class="mt-1 text-sm text-slate-600">
                Perbarui detail area parkir ini.
            </p>
        </div>
        <a href="{{ route('ticket.area') }}" class="btn btn-quiet">Batal</a>
    </div>

    @include('area.partials.form', ['item' => $area, 'petugasOptions' => $petugasOptions])
@endsection