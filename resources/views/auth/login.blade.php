@extends('layouts.app')

@section('title', 'park.')
@section('page', 'login')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rise rounded-3xl border border-primary-100 bg-white p-8 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-primary-500 text-white">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                        <path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Masuk</h1>
                    <p class="text-sm text-slate-500">Gunakan akun admin atau petugas.</p>
                </div>
            </div>

            <form id="login-form" class="mt-6 space-y-4" novalidate>
                <div>
                    <label for="login-username" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Username</label>
                    <input id="login-username" name="username" type="text" autocomplete="username" required
                        class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="cth: petugas" />
                </div>
                <div>
                    <label for="login-password" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Password</label>
                    <input id="login-password" name="password" type="password" autocomplete="current-password" required
                        class="mt-1.5 w-full rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-100"
                        placeholder="••••••••" />
                </div>
                <button id="login-submit" type="submit"
                    class="w-full rounded-xl bg-primary-500 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:opacity-60">
                    Masuk
                </button>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:underline">Daftar</a>
            </p>
        </div>
    </div>
@endsection