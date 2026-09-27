@extends('layouts.app')

@section('title', 'park.')
@section('page', 'verifikasi')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rise sheet p-6 sm:p-8">
            @php($rejected = $user->status_verifikasi === 'ditolak')

            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-panel bg-primary-500 text-white">
                    @if ($rejected)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    @endif
                </span>
                <div>
                    <h1 class="font-display text-xl font-bold text-ink">{{ $rejected ? 'Pendaftaran Ditolak' : 'Menunggu Persetujuan' }}</h1>
                    <p class="text-sm text-slate-600">Akun {{ $user->username }} terdaftar sebagai petugas.</p>
                </div>
            </div>

            <p class="mt-5 text-sm text-slate-600">
                @if ($rejected)
                    Admin menolak pendaftaran akun ini, jadi panel petugas tidak bisa dipakai. Jika Anda merasa ini keliru, hubungi admin parkir.
                @else
                    Admin perlu menyetujui pendaftaran ini dulu sebelum panel petugas bisa dibuka. Setelah disetujui, muat ulang halaman ini dan menu petugas akan muncul.
                @endif
            </p>

            <p class="mt-3">
                @if ($rejected)
                    <span class="state bg-red-100 text-red-700">ditolak</span>
                @else
                    <span class="state bg-amber-100 text-amber-800">menunggu verifikasi</span>
                @endif
            </p>

            @if ($rejected)
                <form method="POST" action="{{ route('logout') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="btn btn-quiet w-full">Keluar dari akun</button>
                </form>
            @else
                <a href="{{ route('verifikasi.menunggu') }}" class="btn btn-primary mt-6 w-full">Periksa lagi</a>
            @endif

            <div class="mt-4 border-t border-rule pt-3 text-xs text-slate-600">
                <p class="font-display font-bold uppercase tracking-wide">Info</p>
                <p class="mt-1">Halaman ini terbuka untuk akun yang belum disetujui. Begitu ada persetujuan, tautan ini membuka beranda dan menu petugas.</p>
            </div>
        </div>
    </div>
@endsection