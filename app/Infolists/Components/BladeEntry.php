<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\Blade;

class BladeEntry extends Entry
{
    public static function make(string $name, ?string $blade = null, array $viewData = []): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        if ($blade) {
            $static->blade($blade, $viewData);
        }

        return $static;
    }

    public function toHtml(): string
    {
        return Blade::render($this->getState(), $this->viewData);
    }

    public function blade(?string $blade, array $viewData = [])
    {
        $this->state($blade);
        $this->viewData($viewData);

        return $this;
    }
}
