{{--
    A licence plate, shown as the three fields it is read in: region, serial,
    series. Registering a plate as one run of characters forces the reader to
    find the boundaries themselves every time, which is exactly what goes wrong
    when a plate is dictated over the radio or checked against a printed ticket.

    Pass size="lg" where the plate is the subject of the screen (a ticket, a
    vehicle header) instead of one cell in a column.

    The spaces between the parts are real text, not just the CSS gap, so the
    plate is announced and copied as "B 1234 ABC" and not "B1234ABC".
--}}
@props([
    'value' => null,
    'size' => 'sm',
])

@php
    $parts = \App\Support\Plate::parts((string) $value);
    $class = $size === 'lg' ? 'plate plate-lg' : 'plate';
@endphp

@if ($parts === null)
    <span class="{{ $class }}">{{ $value ?: '—' }}</span>
@else
    <span class="{{ $class }}"><span class="plate-region">{{ $parts['region'] }}</span> <span class="plate-serial">{{ $parts['serial'] }}</span>@if ($parts['series'] !== '') <span class="plate-series">{{ $parts['series'] }}</span>@endif</span>
@endif
