<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ParkEase — Aplikasi Tiket Parkir')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-page="@yield('page', '')" data-auth-role="{{ session('auth_user.role') ?? '' }}" data-auth-name="{{ session('auth_user.nama') ?? '' }}" class="min-h-screen bg-primary-50 font-sans text-slate-800 antialiased">
    @php $path = request()->path(); @endphp

    <header class="sticky top-0 z-40 border-b border-primary-200/60 bg-white shadow-sm">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-6 px-4 py-3 sm:px-6">
            <a href="/" class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-primary-500 text-white">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                        <path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-base font-bold text-primary-700">ParkEase</span>
                    <span class="block text-[11px] uppercase tracking-widest text-slate-400">Tiket Parkir</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 text-sm font-medium md:flex" id="nav-menu">
                @php
                    $nav = [
                        ['/',          'Beranda',   'beranda'],
                        ['/masuk',     'Masuk',     'masuk'],
                        ['/keluar',    'Keluar',    'keluar'],
                        ['/transaksi', 'Transaksi', 'transaksi'],
                        ['/area',      'Area',      'area'],
                        ['/kendaraan', 'Kendaraan', 'kendaraan'],
                    ];
                @endphp
                @foreach ($nav as $item)
                    <a href="{{ $item[0] }}"
                       class="nav-link rounded-lg px-3 py-1.5 {{ $path === $item[0] ? 'is-active' : '' }}">{{ $item[1] }}</a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-2">
                @php $authUser = session('auth_user'); @endphp
                @if ($authUser)
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2 rounded-full border border-primary-200 bg-white py-1 pl-1 pr-3">
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-primary-500 text-[11px] font-bold text-white">{{ mb_strtoupper(mb_substr($authUser['nama'], 0, 1)) }}</span>
                            <span class="hidden leading-tight sm:block">
                                <span class="block text-[11px] font-semibold text-slate-700">{{ $authUser['nama'] }}</span>
                                <span class="block text-[10px] font-semibold uppercase tracking-wide text-primary-600">{{ $authUser['role'] }}</span>
                            </span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-4 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
                        Masuk
                    </a>
                @endif
                <div class="hidden items-center gap-1.5 rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-[11px] font-medium text-primary-700 sm:flex">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 live-dot"></span>
                    <span class="font-mono" id="nav-clock">--:--:--</span>
                </div>
                <button id="nav-toggle" type="button" aria-label="Menu"
                        class="grid h-9 w-9 place-items-center rounded-lg border border-primary-200 text-primary-700 md:hidden">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M4 6 H20 M4 12 H20 M4 18 H20"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @yield('content', '')
    </main>

    <footer class="border-t border-primary-100 py-6 text-center text-xs text-slate-400">
        <span class="font-semibold text-primary-600">ParkEase</span> — aplikasi tiket parkir sederhana · dibuat dengan Laravel
    </footer>

    <div id="toast-wrap" class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-2"></div>
</body>
</html>