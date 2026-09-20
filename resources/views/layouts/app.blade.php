<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'park.')</title>
    <script>
        (function () {
            var stored = null;
            try { stored = localStorage.getItem('park.theme'); } catch (_) {}
            var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (dark) document.documentElement.classList.add('dark');
        })();
    </script>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-primary-50 font-sans text-slate-800 antialiased">
    @php $section = explode('/', trim(request()->path(), '/'))[0]; @endphp

    <a href="#main-content" class="sidebar-skip">Lewati navigasi</a>
    <aside id="app-sidebar" class="app-sidebar" aria-label="Sidebar">
        <div class="sidebar-inner">
            <a href="/" class="sidebar-brand" aria-label="park. — Beranda" data-tooltip="Beranda">
                <span class="sidebar-logo" aria-hidden="true">P</span>
                <span class="sidebar-label leading-tight">
                    <span class="block font-bold text-primary-700 text-2xl">park.</span>
                    <span class="block text-[11px] tracking-widest text-slate-400">aplikasi tiket parkir.</span>
                </span>
            </a>
            <button type="button" id="sidebar-toggle" class="sidebar-action sidebar-toggle" aria-controls="sidebar-navigation" aria-expanded="true" aria-label="Ciutkan sidebar" data-tooltip="Ciutkan sidebar" hidden>
                @include('layouts.sidebar-icon', ['icon' => 'toggle'])
                <span class="sidebar-label">Ciutkan menu</span>
            </button>

            @php
                $nav = [
                    ['/',          'Beranda',   ''],
                    ['/area',      'Area',      'area'],
                    ['/reservasi', 'Reservasi', 'reservasi'],
                ];

                if (session('auth_user')) {
                    $nav = [
                        ['/',                 'Beranda',   ''],
                        ['/masuk',            'Masuk',     'masuk'],
                        ['/keluar',           'Keluar',    'keluar'],
                        ['/transaksi',        'Transaksi', 'transaksi'],
                        ['/area',             'Area',      'area'],
                        ['/kendaraan',        'Kendaraan', 'kendaraan'],
                        ['/tarif',            'Tarif',     'tarif'],
                        ['/reservasi/daftar', 'Reservasi', 'reservasi'],
                        ['/log',              'Log',       'log'],
                    ];
                }
            @endphp

            <nav id="sidebar-navigation" class="sidebar-navigation" aria-label="Navigasi utama">
                @foreach ($nav as $item)
                    <a href="{{ $item[0] }}" aria-label="{{ $item[1] }}" data-tooltip="{{ $item[1] }}"
                       @if ($item[2] === $section) aria-current="page" @endif
                       class="sidebar-action {{ $item[2] === $section ? 'is-active' : '' }}">
                        @include('layouts.sidebar-icon', ['icon' => $item[2] ?: 'home'])
                        <span class="sidebar-label">{{ $item[1] }}</span>
                    </a>
                @endforeach
            </nav>

            <button type="button" id="theme-toggle" class="flex justify-center text-slate-700 hover:-translate-y-[5px] hover:bg-slate-500 rounded w-fit p-[10px] transition-all 200ms ease-in-out"
                    aria-pressed="false" aria-label="Ganti mode gelap / terang" data-tooltip="Mode Gelap">
                @include('layouts.sidebar-icon', ['icon' => 'moon', 'iconClass' => 'sidebar-icon theme-icon-moon'])
                @include('layouts.sidebar-icon', ['icon' => 'sun', 'iconClass' => 'sidebar-icon theme-icon-sun'])
            </button>

            <div class="sidebar-account">
                @php $authUser = session('auth_user'); @endphp
                @if ($authUser)
                    <div class="sidebar-profile" title="{{ $authUser['nama'] }} — {{ $authUser['role'] }}">
                        <span class="sidebar-avatar">{{ mb_strtoupper(mb_substr($authUser['nama'], 0, 1)) }}</span>
                        <span class="sidebar-label min-w-0 leading-tight">
                            <span class="block truncate text-xs font-semibold text-slate-700">{{ $authUser['nama'] }}</span>
                            <span class="block text-[10px] font-semibold uppercase tracking-wide text-primary-600">{{ $authUser['role'] }}</span>
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="sidebar-action sidebar-logout" aria-label="Keluar dari akun" data-tooltip="Keluar dari akun">
                            @include('layouts.sidebar-icon', ['icon' => 'logout'])
                            <span class="sidebar-label">Keluar</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="sidebar-action" aria-label="Daftar" data-tooltip="Daftar">
                        @include('layouts.sidebar-icon', ['icon' => 'register'])
                        <span class="sidebar-label">Daftar</span>
                    </a>
                    <a href="{{ route('login') }}" class="sidebar-action sidebar-login" aria-label="Masuk ke akun" data-tooltip="Masuk ke akun">
                        @include('layouts.sidebar-icon', ['icon' => 'login'])
                        <span class="sidebar-label">Masuk</span>
                    </a>
                @endif
            </div>
        </div>
    </aside>
    <div id="sidebar-tooltip" class="sidebar-tooltip" role="tooltip" hidden></div>

    <div class="sidebar-content">
    <main id="main-content" tabindex="-1" class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @if (session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali isian Anda:</p>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content', '')
    </main>
    </div>
    @include('layouts.sidebar-script')
</body>
</html>