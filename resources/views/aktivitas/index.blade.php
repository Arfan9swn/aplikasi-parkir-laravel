@extends('layouts.app')

@section('title', 'Log — park.')
@section('page', 'log')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Pusat Log</h1>
            <p class="mt-1 text-sm text-slate-600">Pantau aktivitas pengguna dan log sistem.</p>
        </div>
        <form method="GET" action="{{ url('/log') }}" class="flex items-center gap-3">
            <input name="q" type="text" value="{{ $q }}" placeholder="Cari aktivitas, nama, atau username…" class="field w-64" />
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>

    @php($errorCount = $staff && $sysLog ? collect($sysLog['tail'] ?? [])->filter(fn ($line) =>
        str_contains(strtolower((string) $line), 'error') ||
        str_contains(strtolower((string) $line), 'critical') ||
        str_contains(strtolower((string) $line), 'exception'))->count() : 0)

    <div class="sheet mb-6">
        <div class="cols-ruled" style="--cols: {{ $admin ? 4 : 3 }}">
            <div>
                <p class="figure-label">Total Log</p>
                <p class="figure mt-1">{{ number_format((int) $total, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Semua aktivitas tercatat</p>
            </div>
            <div>
                <p class="figure-label">Hari Ini</p>
                <p class="figure mt-1">{{ number_format((int) $today, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Sejak tengah malam</p>
            </div>
            <div>
                <p class="figure-label">Pengguna Aktif</p>
                <p class="figure mt-1">{{ number_format((int) $users, 0, ',', '.') }}</p>
                <p class="figure-note mt-0.5">Pengguna terdaftar</p>
            </div>
            @if ($admin)
                <div>
                    <p class="figure-label">Error Sistem</p>
                    <p class="figure mt-1">{{ number_format((int) $errorCount, 0, ',', '.') }}</p>
                    <p class="figure-note mt-0.5">Pada laravel.log (tail)</p>
                </div>
            @endif
        </div>
    </div>

    <section class="mt-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-display text-sm font-bold text-ink">Jejak Aktivitas ({{ $logs->count() }})</h2>
        </div>

        <div class="sheet overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ledger">
                    <thead>
                        <tr>
                            <x-table-sort column="waktu_aktivitas" label="Waktu" :allowed="$allowed" />
                            <x-table-sort column="petugas" label="Petugas" :allowed="$allowed" />
                            <x-table-sort column="aktivitas" label="Aktivitas" :allowed="$allowed" />
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $l)
                            <tr>
                                <td class="num whitespace-nowrap text-xs text-slate-600">
                                    {{ $l->waktu_aktivitas ? \Carbon\Carbon::parse($l->waktu_aktivitas)->format('d M Y · H:i:s') : '-' }}
                                </td>
                                <td>
                                    {{ $l->user->nama_lengkap ?? '-' }}
                                    @if ($l->user->username ?? null)
                                        <span class="text-slate-600">({{ $l->user->username }})</span>
                                    @endif
                                </td>
                                <td>{{ $l->aktivitas }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-0">
                                    <p class="notice m-3">
                                        @if ($q !== '')
                                            Tidak ada aktivitas yang cocok dengan kata kunci ini. Coba nama petugas atau kata lain dari aktivitasnya.
                                        @else
                                            Belum ada aktivitas tercatat, jadi belum ada yang bisa ditelusuri. Baris terisi begitu ada aksi yang dicatat aplikasi.
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

        @if ($admin)
            @if ($sysLog)
            <section class="mt-8">
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-display text-sm font-bold text-ink">Log Sistem — {{ $sysLog['file'] }}</h2>
                        <p class="num text-[11px] text-slate-600">{{ number_format((int) $sysLog['size'], 0, ',', '.') }} byte terbaca</p>
                    </div>
                </div>
                <div class="sheet overflow-hidden">
                    <div class="max-h-[540px] overflow-auto">
                        <pre class="font-mono whitespace-pre-wrap p-3 text-[11px] leading-relaxed text-slate-600">{{ implode("\n", array_map(fn ($line) => e($line), $sysLog['tail'] ?? [])) }}</pre>
                    </div>
                </div>
            </section>
        @else
            <p class="notice mt-6">Tidak ada file log sistem (laravel.log) ditemukan.</p>
        @endif
    @endif
</div>
@endsection
