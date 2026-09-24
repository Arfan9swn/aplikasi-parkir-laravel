@extends('layouts.app')

@section('title', 'park.')
@section('page', 'login')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rise sheet p-6 sm:p-8">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-panel bg-primary-500 text-white">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                        <path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                    </svg>
                </span>
                <div>
                    <h1 class="font-display text-xl font-bold text-ink">Masuk</h1>
                    <p class="text-sm text-slate-600">Gunakan akun admin atau petugas.</p>
                </div>
            </div>

            <form method="POST" action="{{ url('/login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="login-username" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Username</label>
                    <input id="login-username" name="username" type="text" autocomplete="username" required value="{{ old('username') }}"
                        class="field mt-1.5" placeholder="cth: petugas" />
                    @error('username')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="login-password" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Password</label>
                    <input id="login-password" name="password" type="password" autocomplete="current-password" required
                        class="field mt-1.5" placeholder="••••••••" />
                    @error('password')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full">Masuk</button>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:underline">Daftar</a>
            </p>
        </div>
    </div>
@endsection