<?php

namespace App\Classes;

use Illuminate\Contracts\View\View;

class Block extends \Livewire\Component
{
    protected string | null $position = 'right';

    protected string | null $name = "Block";

    protected int | null $sort = 1;

    protected string | View | null $view = null;

    protected bool $active = true;

    public function getViewData(): array
    {
        return [];
    }

    public function getBlockName(): string
    {
        return $this->name;
    }

    public function getSetting(string $name)
    {
        $blockSetting = \App\Models\Block::where('class', static::class)->first();
        return $blockSetting?->{$name};
    }

    public function getPosition(): string | null
    {
        return $this->getSetting('position') ?? $this->position;
    }

    public function getSort(): int | null
    {
        return $this->getSetting('sort') ?? $this->sort;
    }

    public function isActive(): bool
    {
        return $this->getSetting('active') ?? $this->active;
    }

    public function render(): View
    {
        return view($this->view, $this->getViewData());
    }

    public function getSettings()
    {
        return [
            'class' => static::class,
            'name' => $this->getBlockName(),
            'position' => $this->getPosition(),
            'sort' => $this->getSort(),
            'active' => $this->isActive()
        ];
    }
}
