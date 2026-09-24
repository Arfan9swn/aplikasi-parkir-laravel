<?php

use App\Support\Plate;

test('a plate splits into region, serial and series', function () {
    expect(Plate::parts('B 1234 ABC'))
        ->toBe(['region' => 'B', 'serial' => '1234', 'series' => 'ABC']);
});

test('a plate stored without separators still splits', function () {
    expect(Plate::parts('B1234ABC'))
        ->toBe(['region' => 'B', 'serial' => '1234', 'series' => 'ABC']);
});

test('a two letter region and a single letter series both fit', function () {
    expect(Plate::parts('AB 1 A'))
        ->toBe(['region' => 'AB', 'serial' => '1', 'series' => 'A']);
});

test('a plate with no series keeps an empty series', function () {
    expect(Plate::parts('B 1234'))
        ->toBe(['region' => 'B', 'serial' => '1234', 'series' => '']);
});

test('lower case and stray separators are accepted', function () {
    expect(Plate::parts('d 1234 xyz'))->toBe(['region' => 'D', 'serial' => '1234', 'series' => 'XYZ'])
        ->and(Plate::parts('B.1234.AB'))->toBe(['region' => 'B', 'serial' => '1234', 'series' => 'AB'])
        ->and(Plate::parts(' B  1234  AB '))->toBe(['region' => 'B', 'serial' => '1234', 'series' => 'AB']);
});

test('a value that is not a plate is left alone', function () {
    expect(Plate::parts('TNI 1234'))->toBeNull()
        ->and(Plate::parts('B 12345 A'))->toBeNull()
        ->and(Plate::parts('B 1234 ABCD'))->toBeNull()
        ->and(Plate::parts('1234'))->toBeNull()
        ->and(Plate::parts(''))->toBeNull()
        ->and(Plate::parts(null))->toBeNull();
});
