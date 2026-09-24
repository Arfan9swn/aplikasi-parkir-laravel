@extends('layouts.app')

@section('title', 'park.')
@section('page', 'beranda')

@section('content')
    <section class="sheet">
        <div class="sheet-head">
            <p>{{ now()->format('l, d F Y') }}</p>
            <p class="num text-xs font-medium text-slate-600">{{ now()->format('H:i:s') }}</p>
        </div>

        <div class="p-5 sm:p-6">  

            <h1 class="font-display text-3xl font-bold leading-tight text-ink sm:text-4xl">
                Masuk. Parkir. Bayar &amp; selesai.
            </h1>
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-600">
                Aplikasi pencatatan parkir yang sederhana. Cetak tiket saat kendaraan masuk,
                lalu cari lagi dengan nomor polisi saat kendaraan keluar.
            </p>

            <div class="mt-5 flex flex-wrap gap-2">
            @if (session('auth_user'))
                <a href="/masuk" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                        <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                    Masuk Terbitkan Tiket
                </a>
                <a href="/keluar" class="btn btn-quiet">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                    </svg>
                    Keluar Bayar &amp; Pulang
                </a>
            @else
                <a href="{{ route('reservasi.create') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    Reservasi Slot Parkir
                </a>
                <a href="/area" class="btn btn-quiet">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                    Lihat Area &amp; Kendaraan
                </a>
            @endif
            </div>
        </div>
    </section>

    {{-- The figures, ruled as columns. Each one used to sit in its own tinted
         box; the label already names the figure, so the box only repeated the
         label's job in decoration. --}}
    <section class="sheet mt-6" aria-label="Ringkasan parkir">
        <div class="cols-ruled" style="--cols: {{ session('auth_user') ? 4 : 3 }}">
            <div>
                <p class="figure-label">Total Slot</p>
                <p class="figure mt-1">{{ number_format($spots, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Kapasitas parkir</p>
            </div>
            <div>
                <p class="figure-label">Terisi Saat Ini</p>
                <p class="figure mt-1">{{ number_format($occupied, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Slot terpakai</p>
            </div>
            <div>
                <p class="figure-label">Tiket Aktif</p>
                <p class="figure mt-1">{{ number_format($active, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Sedang parkir</p>
            </div>
            @if (session('auth_user'))
                <div>
                    <p class="figure-label">Pendapatan Hari Ini</p>
                    <p class="figure mt-1">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                    <p class="figure-note mt-0.5">Total bayar</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Performance, as one wide chart and one narrow one. Both are sheets with a
         ruled head, so the number in the head reads before the graphic does. --}}
    <section class="mt-8">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="font-display text-lg font-bold text-ink">Performa Area</h2>
            <p class="num text-xs text-slate-600">14 hari terakhir · seluruh area</p>
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

        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            <div class="sheet lg:col-span-2">
                <div class="sheet-head">
                    <h3>Kendaraan Masuk per Hari</h3>
                    <p class="text-[11px] text-slate-600">Arahkan kursor ke titik untuk detail harian.</p>
                </div>
                <div class="p-4">
                <svg viewBox="0 0 600 190" class="w-full" role="img" aria-label="Grafik kendaraan masuk per hari">
                    @for ($i = 0; $i <= 2; $i++)
                        <line class="chart-grid" x1="{{ $padX }}" x2="{{ $w - $padX }}"
                              y1="{{ round($padTop + $i * (($h - $padTop - $padBot) / 2), 1) }}"
                              y2="{{ round($padTop + $i * (($h - $padTop - $padBot) / 2), 1) }}"
                              stroke="#e4ecf4" stroke-dasharray="3 4" stroke-width="1" />
                    @endfor
                    <polygon class="chart-fill" points="{{ $fill }}" fill="#395a7f14" />
                    <polyline class="chart-line" points="{{ $line }}" fill="none" stroke="#395a7f" stroke-width="2.5"
                              stroke-linecap="round" stroke-linejoin="round" />
                    @foreach ($pts as $p)
                        <circle class="chart-dot" cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4"
                                fill="#ffffff" stroke="#395a7f" stroke-width="2"
                                data-tooltip="{{ $p['l'] }} — {{ $p['v'] }} kendaraan masuk" />
                    @endforeach
                    @foreach ($pts as $p)
                        @if ($loop->iteration % 4 === 1 || $loop->last)
                            <text class="chart-label" x="{{ $p['x'] }}" y="{{ $h - 8 }}" text-anchor="middle" font-size="10" fill="#64748b">{{ $p['l'] }}</text>
                        @endif
                    @endforeach
                </svg>
                </div>
            </div>
            @php
                $slices  = $areaPerf->filter(fn ($a) => $a['revenue'] > 0)->values();
                $total   = (float) $slices->sum('revenue');
                $r       = 45;
                $circ    = round(2 * M_PI * $r, 2);
                $offset  = 0.0;
                $palette = ['#395a7f', '#5b84b1', '#7fa8cd', '#66a68a', '#d9a86a', '#b98ea6', '#8fb6c9'];
            @endphp

            <div class="sheet">
                <div class="sheet-head">
                    <h3>Pangsa Pendapatan Area</h3>
                    <p class="text-[11px] text-slate-600">Dari tiket yang sudah selesai.</p>
                </div>

                @if ($total > 0)
                    <div class="flex items-center gap-4 p-4">
                        <svg viewBox="0 0 120 120" class="-rotate-90 h-32 w-32 shrink-0" role="img" aria-label="Pangsa pendapatan per area">
                            <circle class="pie-track" cx="60" cy="60" r="{{ $r }}" fill="none" stroke="#eef3f8" stroke-width="16" />
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
                                    <span class="num min-w-0 flex-1 truncate font-medium text-slate-600">{{ $slice['name'] }}</span>
                                    <span class="num text-slate-600">{{ round($slice['revenue'] / $total * 100) }}%</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="border-t border-rule px-4 py-3 text-[11px] text-slate-600">
                        Total <span class="num font-semibold text-ink">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        dari {{ $slices->sum('tickets') }} tiket selesai.
                    </p>
                @else
                    <p class="notice m-4">
                        Belum ada tiket selesai, jadi pangsa pendapatan belum bisa dihitung.
                        Angka ini terisi setelah petugas memproses pembayaran pertama.
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- The three steps as numbered clauses: ordered, ruled, with the number in
         its own narrow column. Three identical boxes said nothing about order. --}}
    <section class="sheet mt-8">
        <div class="sheet-head">
            <h2>Cara Pakai</h2>
        </div>
        <ol>
            <li class="clause">
                <span class="clause-number">1</span>
                <div>
                    <h3 class="font-semibold text-ink">Kendaraan masuk</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Tulis nomor polisi, pilih area dan jenis kendaraan — tiket masuk langsung jadi.</p>
                </div>
            </li>
            <li class="clause">
                <span class="clause-number">2</span>
                <div>
                    <h3 class="font-semibold text-ink">Pantau langsung</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Setiap area menampilkan kepadatan parkir secara langsung, lengkap dengan lama parkir dan perkiraan biaya.</p>
                </div>
            </li>
            <li class="clause">
                <span class="clause-number">3</span>
                <div>
                    <h3 class="font-semibold text-ink">Keluar &amp; bayar</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Cari kendaraan lewat nomor polisi, cek durasinya, bayar biayanya, lalu slot langsung kosong lagi.</p>
                </div>
            </li>
        </ol>
    </section>

    <section class="mt-8">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="font-display text-lg font-bold text-ink">Kondisi Parkir Saat Ini</h2>
            <a href="/area" class="text-xs font-semibold text-primary-600 transition hover:text-primary-700">Lihat semua area →</a>
        </div>
        <div class="sheet mt-4 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ledger">
                    <thead>
                        <tr>
                            <th>Area</th>
                            <th class="text-right">Terisi</th>
                            <th class="text-right">Kapasitas</th>
                            <th>Kepadatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($areas as $a)
                            @php
                                $pct  = $a->kapasitas > 0 ? min(100, ($a->terisi / $a->kapasitas) * 100) : 0;
                                $full = $a->kapasitas > 0 && $a->terisi >= $a->kapasitas;
                            @endphp
                            <tr>
                                <td class="font-medium text-ink">{{ $a->nama_area }}</td>
                                <td class="num text-right">{{ $a->terisi }}</td>
                                <td class="num text-right text-slate-600">{{ $a->kapasitas }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 w-24 bg-primary-100">
                                            <div class="h-full {{ $full ? 'bg-red-500' : 'bg-primary-500' }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="num text-xs font-semibold {{ $full ? 'text-red-600' : 'text-slate-600' }}">
                                            {{ $full ? 'PENUH' : round($pct) . '%' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-0">
                                    <p class="notice m-3">
                                        Belum ada area parkir yang terdaftar, jadi belum ada slot yang bisa diisi.
                                        @if (session('auth_user'))
                                            Tambahkan lewat menu Area.
                                        @else
                                            Daftar ini terisi setelah petugas mendaftarkan area.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection