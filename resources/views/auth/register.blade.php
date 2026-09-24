@extends('layouts.app')

@section('title', 'park.')
@section('page', 'register')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rise sheet p-6 sm:p-8">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-panel bg-primary-500 text-white">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M19 8v6M22 11h-6"/>
                    </svg>
                </span>
                <div>
                    <h1 class="font-display text-xl font-bold text-ink">Daftar Akun</h1>
                    <p class="text-sm text-slate-600">Buat akun petugas parkir baru.</p>
                </div>
            </div>

            <form method="POST" action="{{ url('/registrasi') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="register-nama" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Nama Lengkap</label>
                    <input id="register-nama" name="nama_lengkap" type="text" autocomplete="name" required value="{{ old('nama_lengkap') }}"
                        class="field mt-1.5" placeholder="cth: Budi Santoso" />
                    @error('nama_lengkap')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="register-username" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Username</label>
                    <input id="register-username" name="username" type="text" autocomplete="username" required value="{{ old('username') }}"
                        class="field mt-1.5" placeholder="cth: budi" />
                    @error('username')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="register-password" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Password</label>
                        <input id="register-password" name="password" type="password" autocomplete="new-password" required
                            class="field mt-1.5" placeholder="min. 8 karakter" />
                        @error('password')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="register-password2" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Konfirmasi</label>
                        <input id="register-password2" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="field mt-1.5" placeholder="ulangi password" />
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full">Daftar</button>
            </form>

            <p class="mt-4 text-center text-xs text-slate-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:underline">Masuk</a>
            </p>

            {{-- A note with a rule, not a card: it explains one thing about this form. --}}
            <div class="mt-4 border-t border-rule pt-3 text-xs text-slate-600">
                <p class="font-display font-bold uppercase tracking-wide">Info</p>
                <p class="mt-1">Akun yang didaftarkan di halaman ini otomatis berperan sebagai <span class="font-semibold text-primary-700">petugas</span> dan langsung aktif. Role admin hanya bisa diberikan oleh admin.</p>
            </div>
        </div>
    </div>
@endsection