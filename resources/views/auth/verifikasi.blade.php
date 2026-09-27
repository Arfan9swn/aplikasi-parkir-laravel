@extends('layouts.app')

@section('title', 'park.')
@section('page', 'verifikasi')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rise sheet p-6 sm:p-8">
            @php
                $rejected = $user->status_verifikasi === 'ditolak';
                $needOtp  = ! $rejected && ! $user->hasVerifiedEmail();
            @endphp

            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-panel bg-primary-500 text-white">
                    @if ($rejected)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    @elseif ($needOtp)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-10 5L2 7"/>
                        </svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    @endif
                </span>
                <div>
                    <h1 class="font-display text-xl font-bold text-ink">
                        @if ($rejected)
                            Pendaftaran Ditolak
                        @elseif ($needOtp)
                            Verifikasi Email
                        @else
                            Menunggu Persetujuan
                        @endif
                    </h1>
                    <p class="text-sm text-slate-600">Akun {{ $user->username }} terdaftar sebagai petugas.</p>
                </div>
            </div>

            @unless ($rejected)
                <ol class="mt-4 space-y-1.5 border-t border-rule pt-3 text-xs text-slate-600">
                    <li class="flex items-center justify-between gap-2">
                        <span>1. Verifikasi email</span>
                        @if ($user->hasVerifiedEmail())
                            <span class="state bg-emerald-100 text-emerald-800">terverifikasi</span>
                        @else
                            <span class="state bg-amber-100 text-amber-800">belum</span>
                        @endif
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>2. Persetujuan admin</span>
                        <span class="state bg-amber-100 text-amber-800">menunggu</span>
                    </li>
                </ol>
            @endunless

            <p class="mt-4 text-sm text-slate-600">
                @if ($rejected)
                    Admin menolak pendaftaran akun ini, jadi panel petugas tidak bisa dipakai. Jika Anda merasa ini keliru, hubungi admin parkir.
                @elseif ($needOtp)
                    Kode verifikasi 6 digit sudah dikirim ke {{ $user->email }}. Masukkan kode itu untuk membuktikan email Anda benar, lalu admin meninjau pendaftaran Anda.
                @else
                    Email sudah diverifikasi. Admin perlu menyetujui pendaftaran ini sebelum panel petugas bisa dibuka. Muat ulang halaman ini setelah ada persetujuan.
                @endif
            </p>

            @if ($rejected)
                <p class="mt-3"><span class="state bg-red-100 text-red-700">ditolak</span></p>
                <form method="POST" action="{{ route('logout') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="btn btn-quiet w-full">Keluar dari akun</button>
                </form>
            @elseif ($needOtp)
                <form method="POST" action="{{ route('verifikasi.otp') }}" class="mt-5">
                    @csrf
                    <label for="otp" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Kode Verifikasi</label>
                    <input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                           autocomplete="one-time-code" required autofocus placeholder="000000"
                           class="field mt-1.5 text-center font-mono text-lg tracking-widest" />
                    @error('otp')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn btn-primary mt-4 w-full">Verifikasi Kode</button>
                </form>
                <form method="POST" action="{{ route('verifikasi.otp.resend') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-quiet w-full">Kirim Ulang Kode</button>
                </form>
                <p class="mt-3 text-xs text-slate-600">Kode berlaku selama 10 menit dan hanya bisa dipakai satu kali. Periksa kotak masuk email Anda.</p>
            @else
                <p class="mt-3"><span class="state bg-emerald-100 text-emerald-800">email terverifikasi</span></p>
                <a href="{{ route('verifikasi.menunggu') }}" class="btn btn-primary mt-6 w-full">Periksa lagi</a>
            @endif

            @unless ($needOtp)
                <div class="mt-4 border-t border-rule pt-3 text-xs text-slate-600">
                    <p class="font-display font-bold uppercase tracking-wide">Info</p>
                    <p class="mt-1">Halaman ini terbuka untuk akun yang belum disetujui. Begitu ada persetujuan, tautan ini membuka beranda dan menu petugas.</p>
                </div>
            @endunless
        </div>
    </div>
@endsection