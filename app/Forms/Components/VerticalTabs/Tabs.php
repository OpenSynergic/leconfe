<?php

namespace App\Forms\Components\VerticalTabs;

use Closure;
use Filament\Forms\Components\Tabs as ComponentsTabs;

class Tabs extends ComponentsTabs
{
    protected string $view = 'forms.components.vertical-tabs.tabs';

    protected bool|Closure $sticky = false;

    protected string|Closure $position = 'left';

    public function position(string $position): static
    {
        $position = $this->evaluate($position);
        if ($position === 'left' || $position === 'right') {
            $this->position = $position;
        } else {
            throw new \Exception('Invalid position provided. Only "left" and "right" are allowed.');
        }

        return $this;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function sticky(bool|Closure $sticky = true): static
    {
        $this->sticky = $this->evaluate($sticky);

        return $this;
    }

    public function isSticky(): bool
    {
        return $this->sticky;
    }
}
