@php
    $icons = [
        'home' => 'M3 10l9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z',
        'area' => 'M3 5l6-2 6 2 6-2v16l-6 2-6-2-6 2z M9 3v16 M15 5v16',
        'masuk' => 'M14 3h6v18h-6 M3 12h12 M9 6l6 6-6 6',
        'keluar' => 'M10 3H4v18h6 M10 12h11 M15 6l6 6-6 6',
        'transaksi' => 'M6 3h12v18l-3-2-3 2-3-2-3 2z M9 7h6 M9 11h6 M9 15h3',
        'kendaraan' => 'M4 11l2-6h12l2 6 M3 11h18v7H3z M5 18v3 M19 18v3 M6 14h2 M16 14h2',
        'tarif' => 'M3 3h8l10 10-8 8L3 11z M7 7h.01',
        'reservasi' => 'M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z M8 3v4 M16 3v4 M3 11h18 M8 16l3 3 5-5',
        'log' => 'M12 3a9 9 0 1 0 9 9 9 9 0 0 0-9-9 M12 7v5l3 2',
        'toggle' => 'M3 4h18v16H3z M9 4v16 M16 9l-3 3 3 3',
        'login' => 'M15 3h6v18h-6 M3 12h12 M9 6l6 6-6 6',
        'logout' => 'M9 3H3v18h6 M9 12h12 M15 6l6 6-6 6',
        'register' => 'M9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8 M2 21v-2a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v2 M20 8v6 M17 11h6',
    ];
@endphp
<svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <path d="{{ $icons[$icon] }}"/>
</svg>
