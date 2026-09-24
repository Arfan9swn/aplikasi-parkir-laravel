@php
    $base = 'tab';
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
