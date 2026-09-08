@extends('layouts.app')

@section('title', 'Data Kendaraan — ParkEase')
@section('page', 'kendaraan')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Kendaraan</h1>
            <p class="mt-1 text-sm text-slate-500">Semua kendaraan yang terdaftar di gerbang.</p>
        </div>
        <input id="vehicle-search" type="text" placeholder="Cari nomor polisi…"
               class="w-56 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
    </div>

    <div id="vehicle-grid" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="skeleton h-44 rounded-2xl"></div>
        <div class="skeleton h-44 rounded-2xl"></div>
        <div class="skeleton h-44 rounded-2xl"></div>
    </div>
@endsection