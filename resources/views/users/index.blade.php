@extends('layouts.app')

@section('title', 'park.')
@section('page', 'users')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Manajemen Akun</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola role, username, dan password seluruh pengguna aplikasi.</p>
        </div>
        <form method="GET" action="{{ url('/users') }}" class="flex items-center gap-3">
            <input name="q" type="text" value="{{ $q }}" placeholder="Cari username, nama, atau role…" class="field w-64" />
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>

    <p class="num mt-4 text-xs text-slate-600">{{ $users->count() }} akun</p>

    <div class="sheet mt-3 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="ledger">
                <thead>
                    <tr>
                        <x-table-sort column="username" label="Username" :allowed="['username', 'nama_lengkap', 'role']" />
                        <x-table-sort column="nama_lengkap" label="Nama Lengkap" :allowed="['username', 'nama_lengkap', 'role']" />
                        <x-table-sort column="role" label="Role" :allowed="['username', 'nama_lengkap', 'role']" />
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td class="font-mono text-ink">{{ $u->username }}@if ((int) $u->id_user === $me)<span class="state ml-1.5 bg-primary-100 text-primary-700">Anda</span>@endif</td>
                            <td>{{ $u->nama_lengkap }}</td>
                            <td>
                                @php
                                    $isOwner  = $u->role === 'owner';
                                    $canTouch = $actorRole === 'owner' || $u->role === 'petugas';
                                @endphp
                                <span class="state">{{ $u->role }}</span>
                                @if ((int) $u->status_aktif !== 1)
                                    <span class="state ml-1 bg-red-100 text-red-700">nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if ((int) $u->id_user === $me)
                                    <span class="text-xs text-slate-600">— akun Anda —</span>
                                @elseif ($isOwner)
                                    <div class="flex flex-wrap items-center gap-2">
                                        <details class="relative">
                                            <summary class="btn btn-primary cursor-pointer list-none"
                                                     data-tooltip="Pindahkan status owner ke akun lain — hanya boleh ada satu owner">Transfer Owner</summary>
                                            <form method="POST" action="{{ route('pengguna.ownership') }}"
                                                  class="absolute right-0 z-20 mt-1 w-64 space-y-2 sheet p-3 shadow-lg"
                                                  onsubmit="return confirm('Pindahkan kepemilikan? Owner saat ini akan menjadi admin.')">
                                                @csrf
                                                @method('PUT')
                                                <label class="block text-[10px] font-semibold uppercase tracking-wide text-slate-600">Owner baru
                                                    <select name="target_id" required
                                                            class="filter-select mt-1 w-full px-2 py-1.5 text-xs">
                                                        @foreach ($users->where('role', '!==', 'owner') as $candidate)
                                                            <option value="{{ $candidate->id_user }}">{{ $candidate->username }} — {{ $candidate->nama_lengkap }} ({{ $candidate->role }})</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                <button type="submit" class="btn btn-primary w-full">Pindahkan</button>
                                            </form>
                                        </details>
                                    </div>
                                @elseif ($canTouch)
                                    <div class="flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('pengguna.role', $u->id_user) }}" class="m-0 flex items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" aria-label="Role untuk {{ $u->username }}"
                                                    data-tooltip="Ubah role {{ $u->username }}"
                                                    class="filter-select px-2 py-1 text-xs">
                                                @foreach (['petugas', 'admin'] as $r)
                                                    <option value="{{ $r }}" @selected($u->role === $r)>{{ $r }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-primary"
                                                    data-tooltip="Terapkan role yang dipilih">Set</button>
                                        </form>
                                        <details class="relative">
                                            <summary class="btn btn-quiet cursor-pointer list-none"
                                                     data-tooltip="Ganti password {{ $u->username }}">Reset Password</summary>
                                            <form method="POST" action="{{ route('pengguna.password', $u->id_user) }}"
                                                  class="absolute right-0 z-20 mt-1 w-64 space-y-2 sheet p-3 shadow-lg">
                                                @csrf
                                                @method('PUT')
                                                <input type="password" name="password" placeholder="Password baru" required minlength="8"
                                                       class="field text-xs" />
                                                <input type="password" name="password_confirmation" placeholder="Ulangi password" required minlength="8"
                                                       class="field text-xs" />
                                                <button type="submit" class="btn btn-primary w-full">Simpan Password</button>
                                            </form>
                                        </details>
                                        <details class="relative">
                                            <summary class="btn btn-quiet cursor-pointer list-none"
                                                     data-tooltip="Edit username dan nama {{ $u->username }}">Edit</summary>
                                            <form method="POST" action="{{ route('pengguna.profile', $u->id_user) }}"
                                                  class="absolute right-0 z-20 mt-1 w-64 space-y-2 sheet p-3 shadow-lg">
                                                @csrf
                                                @method('PUT')
                                                <label class="block text-[10px] font-semibold uppercase tracking-wide text-slate-600">Username
                                                    <input type="text" name="username" value="{{ $u->username }}" required minlength="3"
                                                           class="field mt-1 text-xs" />
                                                </label>
                                                <label class="block text-[10px] font-semibold uppercase tracking-wide text-slate-600">Nama lengkap
                                                    <input type="text" name="nama_lengkap" value="{{ $u->nama_lengkap }}" required
                                                           class="field mt-1 text-xs" />
                                                </label>
                                                <button type="submit" class="btn btn-primary w-full">Simpan Profil</button>
                                            </form>
                                        </details>
                                        @if ($u->role === 'petugas')
                                            <form method="POST" action="{{ route('pengguna.destroy', $u->id_user) }}" class="m-0"
                                                  onsubmit="return confirm('Hapus akun {{ $u->username }}? Akun dengan riwayat data akan dinonaktifkan saja.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" data-tooltip="Hapus akun worker ini">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-600" data-tooltip="Akun admin lain tidak dapat diubah — hanya worker (petugas)">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-0">
                                <p class="notice m-3">
                                    @if ($q !== '')
                                        Tidak ada akun yang cocok dengan kata kunci ini. Coba username, nama, atau role lain.
                                    @else
                                        Tidak ada akun ditemukan. Akun daftar sendiri lewat halaman Daftar, dan role diatur dari tabel ini.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
@endsection
