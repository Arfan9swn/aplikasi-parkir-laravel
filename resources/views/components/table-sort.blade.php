@php
    $classes = $active
        ? 'text-primary-700 cursor-default'
        : 'text-slate-500 hover:text-primary-700 cursor-pointer select-none';
@endphp
<th class="sort-header" @if ($active) aria-sort="{{ $asc ? 'ascending' : 'descending' }}" @endif>
    <a href="{{ $sortUrl() }}"
       class="{{ $classes }}"
       aria-label="{{ $asc ? __('Urutkan :label menurun', ['label' => $label]) : __('Urutkan :label menaik', ['label' => $label]) }}"
       data-tooltip="{{ $asc ? 'Klik untuk urut menurun' : 'Klik untuk urut menaik' }}">
        <span class="inline-flex items-center gap-1">
            {{ $label }}
            <svg class="sort-arrow {{ $active ? 'is-active' : '' }} {{ $active && $asc ? 'is-asc' : '' }}"
                 width="12" height="12" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M12 19V5M5 12l7-7 7 7"/>
            </svg>
        </span>
    </a>
</th>
