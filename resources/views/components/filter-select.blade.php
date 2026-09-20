@props([
    'options' => [],
    'current' => null,
    'baseUrl' => '',
    'param'   => 'area',
    'label'   => 'Filter area',
])

@php
    $matches = fn ($option) => (string) ($option['value'] ?? '') === (string) $current;
@endphp
<label class="filter-select">
    <span class="sr-only">{{ $label }}</span>
    <select data-navigate base-url="{{ $baseUrl }}" param="{{ $param }}" aria-label="{{ $label }}">
        @foreach ($options as $option)
            <option value="{{ $option['value'] ?? '' }}" @selected($matches($option))>
                {{ $option['label'] ?? '' }}
            </option>
        @endforeach
    </select>
</label>

<script>
    document.querySelectorAll('select[data-navigate]').forEach((select) => {
        select.addEventListener('change', () => {
            const url = new URL(select.dataset.baseUrl, window.location.origin);
            if (select.value !== '') url.searchParams.set(select.dataset.param, select.value);
            window.location.assign(url.toString());
        });
    });
</script>
