<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode verifikasi park.</title>
    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = is_file($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
        $entry = $manifest['resources/css/email.css']['file'] ?? $manifest['resources/css/email.css']['css'][0] ?? null;
        $emailCss = $entry && is_file(public_path('build/' . $entry))
            ? file_get_contents(public_path('build/' . $entry))
            : '';
    @endphp
    <style>{!! $emailCss !!}</style>
</head>
<body class="m-0 bg-ground p-0 font-sans text-ink">
    <div class="ml-auto mr-auto w-full max-w-xl pl-3 pr-3 pt-6 pb-6">
        <div class="rounded-[2px] border border-rule bg-white">
            <div class="pl-7 pr-7 pt-5 pb-5 border-b border-rule">
                <p class="m-0 font-display text-2xl font-bold text-accent">park.</p>
                <p class="mt-1 text-[11px] uppercase tracking-widest text-muted">aplikasi tiket parkir.</p>
            </div>

            <div class="pl-7 pr-7 pt-6 pb-1">
                <p class="text-[15px] leading-relaxed text-ink">Halo, {{ $nama }}.</p>
                <p class="mt-4 text-[15px] leading-relaxed text-ink">Gunakan kode 6 digit di bawah ini untuk memverifikasi email Anda:</p>

                <div class="mt-4 rounded-[2px] border border-rule bg-ground p-4 text-center">
                    <p class="m-0 font-mono text-[32px] font-bold tracking-[0.3em] text-ink">{{ $code }}</p>
                </div>

                <p class="mt-4 text-[13px] leading-relaxed text-muted">Kode berlaku selama 10 menit dan hanya bisa dipakai satu kali.</p>
                <p class="mt-4 text-[13px] leading-relaxed text-muted">Jika Anda tidak mendaftarkan alamat ini, abaikan pesan ini.</p>

                <p class="mt-4 mb-1">
                    <a href="{{ route('verifikasi.menunggu') }}"
                       class="inline-block rounded-[3px] bg-accent pl-5 pr-5 pt-3 pb-3 text-sm font-semibold text-white no-underline hover:bg-accent-strong">Buka Halaman Verifikasi</a>
                </p>
                <p class="mt-4 text-xs leading-relaxed break-all text-muted">Tombol tidak membuka halaman? Salin alamat ini ke browser: {{ route('verifikasi.menunggu') }}</p>
            </div>

            <div class="pl-7 pr-7 pt-4 pb-6 mt-5 border-t border-rule">
                <p class="m-0 text-xs leading-relaxed text-muted">Email otomatis dari park. untuk memverifikasi alamat email pada pendaftaran akun petugas.</p>
            </div>
        </div>
    </div>
</body>
</html>
