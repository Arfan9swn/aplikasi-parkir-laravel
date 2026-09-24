<?php

use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    $this->withoutVite();
});

test('the plate component separates the three fields in the text, not only by CSS', function () {
    // The gap between the fields is CSS, so the rendered text has to carry real
    // spaces: otherwise a screen reader announces "B1234ABC".
    expect(trim(strip_tags(Blade::render('<x-plate :value="$plate" />', ['plate' => 'B1234ABC']))))
        ->toBe('B 1234 ABC');
});

test('a plate with no series letters renders two fields', function () {
    expect(trim(strip_tags(Blade::render('<x-plate :value="$plate" />', ['plate' => 'B 1234']))))
        ->toBe('B 1234');
});

test('a long plate gets the larger treatment when asked for it', function () {
    $html = Blade::render('<x-plate :value="$plate" size="lg" />', ['plate' => 'AB 12 CD']);

    expect($html)->toContain('plate-lg')
        ->and(trim(strip_tags($html)))->toBe('AB 12 CD');
});

test('a value that is not a plate is shown as it was stored', function () {
    expect(trim(strip_tags(Blade::render('<x-plate :value="$plate" />', ['plate' => 'TNI 1234']))))
        ->toBe('TNI 1234');
});

test('an empty plate shows the empty mark', function () {
    expect(trim(strip_tags(Blade::render('<x-plate :value="$plate" />', ['plate' => null]))))
        ->toBe('—');
});
