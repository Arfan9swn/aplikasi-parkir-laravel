<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SortTabs extends Component
{
    public array $tabs;
    public string $current;
    public string $field;

    /**
     * @param  array   $tabs   [['value' => 'masuk', 'label' => 'Aktif'], ...]
     * @param  string  $current Selected tab value.
     * @param  string  $field Query param name to toggle (e.g. 'sort').
     */
    public function __construct(array $tabs, string $current = '', string $field = 'sort')
    {
        $this->tabs = $tabs;
        $this->current = $current !== '' ? $current : ($tabs[0]['value'] ?? '');
        $this->field = $field;
    }

    public function urlFor(string $value): string
    {
        return '?' . http_build_query(array_merge(request()->query(), [$this->field => $value]));
    }

    public function isActive(string $value): bool
    {
        return $this->current === $value;
    }

    public function render()
    {
        return view('components.sort-tabs');
    }
}

