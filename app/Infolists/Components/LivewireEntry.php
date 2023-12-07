<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\Blade;

class LivewireEntry extends Entry
{
    public static function make(string $name, ?string $component = null, array $viewData = []): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        if ($component) {
            $static->livewire($component, $viewData);
        }

        return $static;
    }

    public function toHtml(): string
    {
        return Blade::render('@livewire($component, $viewData, key($key))', [
            'component' => $this->getState(),
            'viewData' => $this->viewData,
            'key' => $this->getId(),
        ]);
    }

    public function livewire(?string $component, array $viewData = [])
    {
        $this->state($component);
        $this->viewData($viewData);

        return $this;
    }

    public function lazy(bool $lazy = true)
    {
        $this->viewData(['lazy' => $lazy]);

        return $this;
    }
}
