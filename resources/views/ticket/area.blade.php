@extends('layouts.app')

@section('title', 'Area Parkir — ParkEase')
@section('page', 'area')

@section('content')
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Area Parkir</h1>
        <p class="mt-1 text-sm text-slate-500">Kepadatan parkir di setiap area, diperbarui langsung.</p>
    </div>

    <div class="mt-6 grid grid-cols-3 gap-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-6 w-6 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                    </svg>
                </span>
                Total Slot
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="area-sum-spots">0</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-6 w-6 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                </span>
                Terisi
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700" id="area-sum-occupied">0</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </span>
                Kosong
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-emerald-600" id="area-sum-free">0</p>
        </div>
    </div>

    <div id="area-grid" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="skeleton h-40 rounded-2xl"></div>
        <div class="skeleton h-40 rounded-2xl"></div>
        <div class="skeleton h-40 rounded-2xl"></div>
    </div>
@endsection