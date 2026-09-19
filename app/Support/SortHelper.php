<?php

namespace App\Support;

/**
 * Helpers for the interactive sort controls used across the list pages.
 *
 * All sorting is driven by query-string parameters so it stays fully
 * server-side, cache-friendly, and works without any JavaScript.
 */
class SortHelper
{
    public const DEFAULT_DIRECTION = 'desc';

    /**
     * The column to sort by, validated against an allow-list.
     */
    public static function field(string $input, array $allowed): string
    {
        $allowed = array_values($allowed);
        $clean = strtolower(trim((string) $input)) ?: $allowed[0] ?? '';
        return in_array($clean, $allowed, true) ? $clean : ($allowed[0] ?? '');
    }

    /**
     * The direction — only asc/desc are accepted.
     */
    public static function direction(string $input): string
    {
        $clean = strtolower(trim((string) $input));
        return $clean === 'asc' ? 'asc' : self::DEFAULT_DIRECTION;
    }

    /**
     * Produce a fresh direction for a header that is clicked again.
     */
    public static function nextDirection(string $current): string
    {
        return $current === 'asc' ? 'desc' : 'asc';
    }

    /**
     * A stable sort key for the current request, so the UI can highlight it.
     */
    public static function current(string $input, array $allowed): string
    {
        return self::field($input, $allowed) . ' ' . self::direction($input);
    }

    /**
     * Build the next query string for a given column.
     */
    public static function urlFor(string $column, array $allowed, array $extra = []): string
    {
        $field = self::field($column, $allowed);
        $currentField = self::field((string) request()->query('sort'), $allowed);
        $currentDir = self::direction((string) request()->query('dir'));
        $nextDir = ($field === $currentField) ? self::nextDirection($currentDir) : self::DEFAULT_DIRECTION;

        $query = array_merge(
            request()->query(),
            array_filter($extra, fn ($v) => $v !== null && $v !== ''),
            ['sort' => $field, 'dir' => $nextDir]
        );

        return '?' . http_build_query($query);
    }
}

