<?php

namespace App\Classes;

use Illuminate\Contracts\View\View;

class Block extends \Livewire\Component
{
    protected static string | null $position = 'right';

    protected static int | null $sort = 1;

    protected static string | View | null $view = null;

    protected static bool $active = true;

    public function getViewData(): array
    {
        return [];
    }

    public function getSetting(string $name)
    {
        $blockSetting = \App\Models\Block::where('class', static::class)->first();
        return $blockSetting?->{$name};
    }

    public function getPosition(): string | null
    {
        return $this->getSetting('position') ?? static::$position;
    }

    public function getSort(): int | null
    {
        return $this->getSetting('sort') ?? static::$sort;
    }

    public function isActive(): bool
    {
        return $this->getSetting('active') ?? static::$active;
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }
}
