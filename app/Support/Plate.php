<?php

namespace App\Support;

/**
 * Splits an Indonesian licence plate into the three fields a person reads it in:
 * the region code, the serial number, and the trailing series letters.
 *
 * Plates are stored uppercase with single spaces collapsed, but that only helps
 * when the petugas typed the spaces. "B1234ABC" is stored exactly like that, so
 * the split has to work with or without separators. Anything that does not have
 * the shape of a plate is left alone rather than guessed at.
 */
class Plate
{
    /**
     * The three fields, or null when the value is not a plate this can split.
     *
     * @return array{region: string, serial: string, series: string}|null
     */
    public static function parts(?string $plate): ?array
    {
        // Strip everything that is not a letter or a digit: spaces, dots and
        // dashes are all things people type into the same field.
        $clean = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $plate));

        if ($clean === null || $clean === '') {
            return null;
        }

        // Region (1-2 letters), serial (1-4 digits), series (0-3 letters).
        if (preg_match('/^([A-Z]{1,2})(\d{1,4})([A-Z]{0,3})$/', $clean, $matches) !== 1) {
            return null;
        }

        return [
            'region' => $matches[1],
            'serial' => $matches[2],
            'series' => $matches[3],
        ];
    }
}
