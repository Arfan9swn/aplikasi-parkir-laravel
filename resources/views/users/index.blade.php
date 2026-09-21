@extends('layouts.app')

@section('title', 'park.')
@section('page', 'users')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Akun</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola role, username, dan password seluruh pengguna aplikasi.</p>
        </div>
        <form method="GET" action="{{ url('/users') }}" class="flex items-center gap-3">
            <input name="q" type="text" value="{{ $q }}" placeholder="Cari username, nama, atau role…"
                   class="w-64 rounded-xl border border-primary-200 px-4 py-2 text-sm text-slate-800 placeholder-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200" />
            <button type="submit" class="micro-hover rounded-xl bg-primary-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-600">Cari</button>
        </form>
    </div>

    <p class="mt-4 text-xs text-slate-400">{{ $users->count() }} akun</p>

    <div class="mt-3 overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-primary-100 bg-primary-50 text-[11px] uppercase tracking-wider text-slate-400">
                        <x-table-sort column="username" label="Username" :allowed="['username', 'nama_lengkap', 'role']" />
                        <x-table-sort column="nama_lengkap" label="Nama Lengkap" :allowed="['username', 'nama_lengkap', 'role']" />
                        <x-table-sort column="role" label="Role" :allowed="['username', 'nama_lengkap', 'role']" />
                        <th class="px-4 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr class="border-b border-primary-100 last:border-0">
                            <td class="px-4 py-2 font-mono">{{ $u->username }}@if ((int) $u->id_user === $me)<span class="ml-1.5 rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-semibold text-primary-700">Anda</span>@endif</td>
                            <td class="px-4 py-2">{{ $u->nama_lengkap }}</td>
                            <td class="px-4 py-2">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $u->role === 'owner' ? 'bg-purple-100 text-purple-800' : ($u->role === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">{{ $u->role }}</span>
                            </td>
                            <td class="px-4 py-2">
                                @if ((int) $u->id_user === $me)
                                    <span class="text-xs text-slate-300">— akun Anda —</span>
                                @else
                                    <div class="flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('pengguna.role', $u->id_user) }}" class="m-0 flex items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" aria-label="Role untuk {{ $u->username }}"
                                                    data-tooltip="Ubah role {{ $u->username }}"
                                                    class="filter-select rounded-lg border border-primary-200 px-2 py-1 text-xs">
                                                @foreach (['petugas', 'admin', 'owner'] as $r)
                                                    <option value="{{ $r }}" @selected($u->role === $r)>{{ $r }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="micro-hover rounded-lg bg-primary-500 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-primary-600"
                                                    data-tooltip="Terapkan role yang dipilih">Set</button>
                                        </form>
                                        <details class="relative">
                                            <summary class="micro-hover cursor-pointer list-none rounded-lg border border-primary-200 px-2.5 py-1 text-xs font-semibold text-primary-700 transition hover:bg-primary-50"
                                                     data-tooltip="Ganti password {{ $u->username }}">Reset Password</summary>
                                            <form method="POST" action="{{ route('pengguna.password', $u->id_user) }}"
                                                  class="absolute right-0 z-20 mt-1 w-60 space-y-2 rounded-xl border border-primary-100 bg-white p-3 shadow-lg">
                                                @csrf
                                                @method('PUT')
                                                <input type="password" name="password" placeholder="Password baru" required minlength="8"
                                                       class="w-full rounded-lg border border-primary-200 px-2 py-1.5 text-xs" />
                                                <input type="password" name="password_confirmation" placeholder="Ulangi password" required minlength="8"
                                                       class="w-full rounded-lg border border-primary-200 px-2 py-1.5 text-xs" />
                                                <button type="submit" class="w-full rounded-lg bg-primary-500 px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-600">Simpan Password</button>
                                            </form>
                                        </details>
                                        <details class="relative">
                                            <summary class="micro-hover cursor-pointer list-none rounded-lg border border-primary-200 px-2.5 py-1 text-xs font-semibold text-primary-700 transition hover:bg-primary-50"
                                                     data-tooltip="Edit username dan nama {{ $u->username }}">Edit</summary>
                                            <form method="POST" action="{{ route('pengguna.profile', $u->id_user) }}"
                                                  class="absolute right-0 z-20 mt-1 w-60 space-y-2 rounded-xl border border-primary-100 bg-white p-3 shadow-lg">
                                                @csrf
                                                @method('PUT')
                                                <label class="block text-[10px] font-semibold uppercase tracking-wide text-slate-400">Username
                                                    <input type="text" name="username" value="{{ $u->username }}" required minlength="3"
                                                           class="mt-1 w-full rounded-lg border border-primary-200 px-2 py-1.5 text-xs" />
                                                </label>
                                                <label class="block text-[10px] font-semibold uppercase tracking-wide text-slate-400">Nama lengkap
                                                    <input type="text" name="nama_lengkap" value="{{ $u->nama_lengkap }}" required
                                                           class="mt-1 w-full rounded-lg border border-primary-200 px-2 py-1.5 text-xs" />
                                                </label>
                                                <button type="submit" class="w-full rounded-lg bg-primary-500 px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-600">Simpan Profil</button>
                                            </form>
                                        </details>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-slate-300">Tidak ada akun ditemukan.</td></tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
@endsection
