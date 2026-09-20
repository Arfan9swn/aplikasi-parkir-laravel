@extends('layouts.app')

@section('title', 'park.')
@section('page', 'beranda')

@section('content')
    <section class="rounded-2xl bg-primary-500 p-8 shadow-sm sm:p-10">  

        <h1 class="mt-4 text-3xl font-extrabold text-white sm:text-4xl">
            Masuk. Parkir. Bayar &amp; selesai.
        </h1>
        <p class="mt-3 max-w-xl text-base text-primary-100">
            Aplikasi pencatatan parkir yang sederhana. Cetak tiket saat kendaraan masuk,
            lalu cari lagi dengan nomor polisi saat kendaraan keluar.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            @if (session('auth_user'))
                <a href="/masuk"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-primary-700 shadow-sm transition hover:bg-primary-50">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                        <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                    Masuk Terbitkan Tiket
                </a>
                <a href="/keluar"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/40 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                    </svg>
                    Keluar Bayar &amp; Pulang
                </a>
            @else
                <a href="{{ route('reservasi.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-primary-700 shadow-sm transition hover:bg-primary-50">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    Reservasi Slot Parkir
                </a>
                <a href="/area"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/40 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                    Lihat Area &amp; Kendaraan
                </a>
            @endif
        </div>

        <p class="mt-6 font-mono text-sm text-white/85">{{ now()->format('l, d F Y · H:i:s') }}</p>
    </section>

    <section class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm hover:-translate-y-0.5 transition-all duration-400 ease-in-out">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                    </svg>
                </span>
                Total Slot
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format($spots, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Kapasitas parkir</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm hover:-translate-y-0.5 transition-all duration-400 ease-in-out">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                </span>
                Terisi Saat Ini
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format($occupied, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Slot terpakai</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm hover:-translate-y-0.5 transition-all duration-400 ease-in-out">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                </span>
                Tiket Aktif
            </p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format($active, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Sedang parkir</p>
        </div>
        @if (session('auth_user'))
            <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm hover:-translate-y-0.5 transition-all duration-400 ease-in-out">
                <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-50 text-primary-600">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                        </svg>
                    </span>
                    Pendapatan Hari Ini
                </p>
                <p class="mt-1.5 text-2xl font-extrabold text-primary-700">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                <p class="mt-1 text-[11px] text-slate-500">Total bayar</p>
            </div>
        @endif
    </section>

    <section class="mt-10">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-bold text-slate-800">Performa Area</h2>
            <span class="text-xs text-slate-400">14 hari terakhir · seluruh area</span>
        </div>

        @php
            $max    = max($daily->max('tickets'), 1);
            $w      = 600;
            $h      = 190;
            $padX   = 26;
            $padTop = 16;
            $padBot = 30;
            $step   = count($daily) > 1 ? ($w - 2 * $padX) / (count($daily) - 1) : 0;
            $pts    = $daily->map(fn ($d, $i) => [
                'x' => round($padX + $i * $step, 1),
                'y' => round($padTop + (1 - $d['tickets'] / $max) * ($h - $padTop - $padBot), 1),
                'v' => $d['tickets'],
                'l' => $d['label'],
            ]);
            $line = $pts->map(fn ($p) => "{$p['x']},{$p['y']}")->implode(' ');
            $fill = $padX . ',' . ($h - $padBot) . ' ' . $line . ' '
                . round($padX + (count($daily) - 1) * $step, 1) . ',' . ($h - $padBot);
        @endphp

        <div class="mt-5 grid gap-5 lg:grid-cols-3">
            <div class="card-hover rounded-2xl border border-primary-100 bg-white p-6 shadow-sm lg:col-span-2">
                <p class="text-sm font-bold text-slate-700">Kendaraan Masuk per Hari</p>
                <p class="mt-0.5 text-[11px] text-slate-400">Arahkan kursor ke titik untuk detail harian.</p>
                <svg viewBox="0 0 600 190" class="mt-3 w-full" role="img" aria-label="Grafik kendaraan masuk per hari">
                    @for ($i = 0; $i <= 2; $i++)
                        <line x1="{{ $padX }}" x2="{{ $w - $padX }}"
                              y1="{{ round($padTop + $i * (($h - $padTop - $padBot) / 2), 1) }}"
                              y2="{{ round($padTop + $i * (($h - $padTop - $padBot) / 2), 1) }}"
                              stroke="#e4ecf4" stroke-dasharray="3 4" stroke-width="1" />
                    @endfor
                    <polygon points="{{ $fill }}" fill="#395a7f14" />
                    <polyline points="{{ $line }}" fill="none" stroke="#395a7f" stroke-width="2.5"
                              stroke-linecap="round" stroke-linejoin="round" />
                    @foreach ($pts as $p)
                        <circle class="chart-dot" cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4"
                                fill="#ffffff" stroke="#395a7f" stroke-width="2"
                                data-tooltip="{{ $p['l'] }} — {{ $p['v'] }} kendaraan masuk" />
                    @endforeach
                    @foreach ($pts as $p)
                        @if ($loop->iteration % 4 === 1 || $loop->last)
                            <text x="{{ $p['x'] }}" y="{{ $h - 8 }}" text-anchor="middle" font-size="10" fill="#94a3b8">{{ $p['l'] }}</text>
                        @endif
                    @endforeach
                </svg>
            </div>
            @php
                $slices  = $areaPerf->filter(fn ($a) => $a['revenue'] > 0)->values();
                $total   = (float) $slices->sum('revenue');
                $r       = 45;
                $circ    = round(2 * M_PI * $r, 2);
                $offset  = 0.0;
                $palette = ['#395a7f', '#5b84b1', '#7fa8cd', '#66a68a', '#d9a86a', '#b98ea6', '#8fb6c9'];
            @endphp

            <div class="card-hover rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-700">Pangsa Pendapatan Area</p>
                <p class="mt-0.5 text-[11px] text-slate-400">Dari tiket yang sudah selesai.</p>

                @if ($total > 0)
                    <div class="mt-3 flex items-center gap-5">
                        <svg viewBox="0 0 120 120" class="-rotate-90 h-32 w-32 shrink-0" role="img" aria-label="Pangsa pendapatan per area">
                            <circle cx="60" cy="60" r="{{ $r }}" fill="none" stroke="#eef3f8" stroke-width="16" />
                            @foreach ($slices as $slice)
                                <circle class="pie-slice" cx="60" cy="60" r="{{ $r }}" fill="none"
                                        stroke="{{ $palette[$loop->index % count($palette)] }}" stroke-width="16"
                                        stroke-dasharray="{{ round($circ * $slice['revenue'] / $total, 2) }} {{ $circ }}"
                                        stroke-dashoffset="{{ round(-$offset, 2) }}"
                                        data-tooltip="{{ $slice['name'] }} — Rp {{ number_format($slice['revenue'], 0, ',', '.') }} ({{ round($slice['revenue'] / $total * 100) }}%)" />
                                @php $offset += $circ * $slice['revenue'] / $total; @endphp
                            @endforeach
                        </svg>
                        <ul class="min-w-0 flex-1 space-y-2">
                            @foreach ($slices as $slice)
                                <li class="flex items-center gap-2 text-xs">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $palette[$loop->index % count($palette)] }}"></span>
                                    <span class="min-w-0 flex-1 truncate font-medium text-slate-600">{{ $slice['name'] }}</span>
                                    <span class="font-mono text-slate-400">{{ round($slice['revenue'] / $total * 100) }}%</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="mt-3 text-[11px] text-slate-400">
                        Total <span class="font-semibold text-slate-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        dari {{ $slices->sum('tickets') }} tiket selesai.
                    </p>
                @else
                    <p class="mt-6 rounded-xl border border-dashed border-primary-200 p-6 text-center text-xs text-slate-400">
                        Belum ada tiket selesai — grafik akan terisi setelah ada pembayaran.
                    </p>
                @endif
            </div>
        </div>
    </section>

    <section class="mt-10">
        <h2 class="text-xl font-bold text-slate-800">Cara Pakai</h2>
        <div class="mt-5 grid gap-5 md:grid-cols-3">
            <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <p class="grid h-9 w-9 place-items-center rounded-full bg-primary-500 text-sm font-bold text-white">1</p>
                <h3 class="mt-3 font-semibold text-slate-800">Kendaraan masuk</h3>
                <p class="mt-1.5 text-sm text-slate-500">Tulis nomor polisi, pilih area dan jenis kendaraan — tiket masuk langsung jadi.</p>
            </div>
            <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <p class="grid h-9 w-9 place-items-center rounded-full bg-primary-500 text-sm font-bold text-white">2</p>
                <h3 class="mt-3 font-semibold text-slate-800">Pantau langsung</h3>
                <p class="mt-1.5 text-sm text-slate-500">Setiap area menampilkan kepadatan parkir secara langsung, lengkap dengan lama parkir dan perkiraan biaya.</p>
            </div>
            <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm">
                <p class="grid h-9 w-9 place-items-center rounded-full bg-primary-500 text-sm font-bold text-white">3</p>
                <h3 class="mt-3 font-semibold text-slate-800">Keluar &amp; bayar</h3>
                <p class="mt-1.5 text-sm text-slate-500">Cari kendaraan lewat nomor polisi, cek durasinya, bayar biayanya, lalu slot langsung kosong lagi.</p>
            </div>
        </div>
    </section>

    <section class="mt-10">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">Kondisi Parkir Saat Ini</h2>
            <a href="/area" class="text-sm font-semibold text-primary-600 transition hover:text-primary-700">Lihat semua area →</a>
        </div>
        <div class="mt-5 space-y-4">
            @forelse ($areas as $a)
                <div class="rise">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-slate-700">{{ $a->nama_area }}</span>
                        <span class="text-xs text-slate-500">{{ $a->terisi }} / {{ $a->kapasitas }}</span>
                    </div>
                    <div class="mt-1.5 h-2 rounded-full bg-primary-100 overflow-hidden">
                        @php $pct = $a->kapasitas > 0 ? min(100, ($a->terisi / $a->kapasitas) * 100) : 0; @endphp
                        <div class="h-full rounded-full {{ $a->terisi >= $a->kapasitas ? 'bg-red-400' : 'bg-primary-500' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="rounded-2xl border border-primary-100 bg-white p-8 text-center text-sm text-slate-400">Belum ada area parkir.</p>
            @endforelse
        </div>
    </section>
@endsection