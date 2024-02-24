<?php

namespace App\Classes;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

abstract class Block implements Htmlable
{
    protected ?string $position = 'right';

    protected string $name;

    protected ?int $sort = 1;

    protected ?string $view = null;

    protected bool $active = true;

    public function getViewData(): array
    {
        return [
            'id' => $this->getDatabaseName(),
        ];
    }

    public function getPrefixName(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSuffixName(): ?string
    {
        return null;
    }

    public function getDatabaseName(): string
    {
        return Str::of($this->name)->lower()->snake();
    }

    public function getSetting(string $name)
    {
        $blockSetting = \App\Models\Block::where('name', $this->getDatabaseName())->first();

        return $blockSetting?->{$name};
    }

    public function getPosition(): ?string
    {
        return $this->getSetting('position') ?? $this->position;
    }

    public function getSort(): ?int
    {
        return $this->getSetting('sort') ?? $this->sort;
    }

    public function isActive(): bool
    {
        return $this->getSetting('active') ?? $this->active;
    }

    public function toHtml()
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view($this->view, $this->getViewData());
    }

    public function getSettings()
    {
        return [
            'class' => static::class,
            'prefix' => $this->getPrefixName(),
            'name' => $this->getName(),
            'suffix' => $this->getSuffixName(),
            'database_name' => $this->getDatabaseName(),
            'position' => $this->getPosition(),
            'sort' => $this->getSort(),
            'active' => $this->isActive(),
        ];
    }
}
