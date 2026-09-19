<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Support\SortHelper;

class TableSort extends Component
{
    public string $column;
    public string $label;
    public array $allowed;
    public string $currentField;
    public string $currentDir;
    public bool $active;
    public bool $asc;

    public function __construct(string $column, string $label, array $allowed)
    {
        $this->column = $column;
        $this->label = $label;
        $this->allowed = $allowed;
        $this->currentField = SortHelper::field((string) request()->query('sort'), $allowed);
        $this->currentDir = SortHelper::direction((string) request()->query('dir'));
        $this->active = $this->currentField === $column;
        $this->asc = $this->active && $this->currentDir === 'asc';
    }

    public function sortUrl(): string
    {
        return SortHelper::urlFor($this->column, $this->allowed);
    }

    public function render()
    {
        return view('components.table-sort');
    }
}

