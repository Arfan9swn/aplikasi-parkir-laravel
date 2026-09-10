@extends('layouts.app')

@section('title', 'park.')
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

    @php $canManageAreas = in_array(session('auth_user.role') ?? '', ['admin', 'petugas']); @endphp

    @if ($canManageAreas)
        <div class="mt-6 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-500">Daftar area</h2>
            <button id="area-add" type="button"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Area
            </button>
        </div>
    @endif

    <div id="area-grid" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="skeleton h-40 rounded-2xl"></div>
        <div class="skeleton h-40 rounded-2xl"></div>
        <div class="skeleton h-40 rounded-2xl"></div>
    </div>

    @if ($canManageAreas)
        <div id="area-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
            <div class="pop w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 id="area-modal-title" class="text-lg font-bold text-slate-800">Tambah Area Parkir</h3>
                    <button type="button" id="area-close" aria-label="Tutup"
                        class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M18 6 6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="area-form" class="mt-5 space-y-4" novalidate>
                    <div>
                        <label for="area-form-nama" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Area</label>
                        <input id="area-form-nama" type="text" required
                            class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                            placeholder="cth: Area B" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="area-form-kapasitas" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kapasitas</label>
                            <input id="area-form-kapasitas" type="number" min="1" required
                                class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                                placeholder="10" />
                        </div>
                        <div>
                            <label for="area-form-terisi" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Terisi</label>
                            <input id="area-form-terisi" type="number" min="0" required
                                class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                                placeholder="0" />
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" id="area-cancel"
                            class="flex-1 rounded-xl border border-primary-200 bg-white py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-primary-50">
                            Batal
                        </button>
                        <button id="area-submit" type="submit"
                            class="flex-1 rounded-xl bg-primary-500 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:opacity-60">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection