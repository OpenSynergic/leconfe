<?php

namespace App\Infolists\Components\VerticalTabs;

use Closure;
use Filament\Infolists\Components\Tabs as ComponentsTabs;

class Tabs extends ComponentsTabs
{
    protected string $view = 'infolists.components.vertical-tabs.tabs';

    protected bool|Closure $sticky = false;

    protected string|Closure $position = 'left';

    protected string|Closure $spaceY = '';

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

    public function spaceY(string $spaceY): static
    {
        $spaceY = $this->evaluate($spaceY);
        if (str_starts_with($spaceY, 'space-y-')){
            $this->spaceY = $spaceY;
        } else {
            throw new \Exception('Invalid spaceY provided. Only "space-y-" are allowed.');
        }

        return $this;
    }

    public function getSpaceY(): string
    {
        return $this->spaceY;
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
