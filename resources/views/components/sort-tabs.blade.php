@php
    $base = 'filter-pill rounded-full px-3 py-1.5 text-xs font-semibold whitespace-nowrap';
@endphp
@foreach ($tabs as $tab)
    <a href="{{ $urlFor($tab['value']) }}"
       class="{{ $base }} {{ $isActive($tab['value']) ? 'is-active' : '' }}"
       @if ($isActive($tab['value'])) aria-current="page" @endif
       data-tooltip="{{ $tab['description'] ?? $tab['label'] }}"
       aria-label="{{ $tab['label'] }}">
        {{ $tab['label'] }}
    </a>
@endforeach
