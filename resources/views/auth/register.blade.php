@extends('layouts.app')

@section('title', 'park.')
@section('page', 'register')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rise rounded-3xl border border-primary-100 bg-white p-8 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-primary-500 text-white">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M19 8v6M22 11h-6"/>
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Daftar Akun</h1>
                    <p class="text-sm text-slate-500">Buat akun petugas parkir baru.</p>
                </div>
            </div>

            <form id="register-form" class="mt-6 space-y-4" novalidate>
                <div>
                    <label for="register-nama" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Lengkap</label>
                    <input id="register-nama" name="nama_lengkap" type="text" autocomplete="name" required
                        class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="cth: Budi Santoso" />
                </div>
                <div>
                    <label for="register-username" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Username</label>
                    <input id="register-username" name="username" type="text" autocomplete="username" required
                        class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="cth: budi" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="register-password" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Password</label>
                        <input id="register-password" name="password" type="password" autocomplete="new-password" required
                            class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                            placeholder="min. 8 karakter" />
                    </div>
                    <div>
                        <label for="register-password2" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Konfirmasi</label>
                        <input id="register-password2" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                            placeholder="ulangi password" />
                    </div>
                </div>
                <button id="register-submit" type="submit"
                    class="w-full rounded-xl bg-primary-500 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:opacity-60">
                    Daftar
                </button>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:underline">Masuk</a>
            </p>

            <div class="mt-4 rounded-xl border border-primary-100 bg-primary-50/60 p-4 text-xs text-slate-500">
                <p class="font-semibold text-slate-600">Info</p>
                <p class="mt-1">Akun yang didaftarkan di halaman ini otomatis berperan sebagai <span class="font-semibold text-slate-600">petugas</span> dan langsung aktif. Role admin hanya bisa diberikan oleh admin.</p>
            </div>
        </div>
    </div>
@endsection