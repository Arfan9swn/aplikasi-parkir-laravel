@extends('layouts.app')

@section('title', 'Log — park.')
@section('page', 'log')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pusat Log</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau aktivitas pengguna dan log sistem.</p>
        </div>
        <form method="GET" action="{{ url('/log') }}" class="flex items-center gap-3">
            <input name="q" type="text" value="{{ $q }}" placeholder="Cari aktivitas, nama, atau username…"
                   class="w-64 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            <button type="submit"
                    class="rounded-xl bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">
                Cari
            </button>
        </form>
    </div>

    @php($errorCount = $staff && $sysLog ? collect($sysLog['tail'] ?? [])->filter(fn ($line) =>
        str_contains(strtolower((string) $line), 'error') ||
        str_contains(strtolower((string) $line), 'critical') ||
        str_contains(strtolower((string) $line), 'exception'))->count() : 0)

    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Log</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format((int) $total, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Semua aktivitas tercatat</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Hari Ini</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format((int) $today, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Sejak tengah malam</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pengguna Aktif</p>
            <p class="mt-1.5 text-2xl font-extrabold text-primary-700">{{ number_format((int) $users, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-300">Pengguna terdaftar</p>
        </div>
        @if ($admin)
            <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Error Sistem</p>
                <p class="mt-1.5 text-2xl font-extrabold text-red-600">{{ number_format((int) $errorCount, 0, ',', '.') }}</p>
                <p class="mt-1 text-[11px] text-slate-300">Pada laravel.log (tail)</p>
            </div>
        @endif
    </div>

    <section class="mt-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-700">Jejak Aktivitas ({{ $logs->count() }})</h2>
        </div>

        <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                            <x-table-sort column="waktu_aktivitas" label="Waktu" :allowed="$allowed" />
                            <x-table-sort column="petugas" label="Petugas" :allowed="$allowed" />
                            <x-table-sort column="aktivitas" label="Aktivitas" :allowed="$allowed" />
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $l)
                            <tr class="border-b border-primary-100 last:border-0">
                                <td class="px-4 py-2 font-mono text-xs text-slate-600">
                                    {{ $l->waktu_aktivitas ? \Carbon\Carbon::parse($l->waktu_aktivitas)->format('d M Y · H:i:s') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-slate-600">
                                    {{ $l->user->nama_lengkap ?? '-' }}
                                    @if ($l->user->username ?? null)
                                        <span class="text-slate-400">({{ $l->user->username }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-slate-700">{{ $l->aktivitas }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-300">Belum ada aktivitas.</td>
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
                        <h2 class="text-sm font-bold text-slate-700">Log Sistem — {{ $sysLog['file'] }}</h2>
                        <p class="text-[11px] text-slate-400">{{ number_format((int) $sysLog['size'], 0, ',', '.') }} byte terbaca</p>
                    </div>
                </div>
                <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
                    <div class="max-h-[540px] overflow-auto">
                        <pre class="font-mono text-[11px] leading-relaxed text-slate-700 whitespace-pre-wrap">{{ implode("\n", array_map(fn ($line) => e($line), $sysLog['tail'] ?? [])) }}</pre>
                    </div>
                </div>
            </section>
        @else
            <p class="mt-6 text-sm text-slate-400">Tidak ada file log sistem (laravel.log) ditemukan.</p>
        @endif
    @endif
</div>
@endsection
