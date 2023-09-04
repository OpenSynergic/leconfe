<?php

namespace App\Forms\Components\VerticalTabs;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Tabs as ComponentsTabs;

class Tabs extends ComponentsTabs
{
    protected string $view = 'forms.components.vertical-tabs.vertical-tabs';

    protected bool $sticky = false;

    public function sticky(bool $sticky = true): static
    {
        $this->sticky = $sticky;

        return $this;
    }

    public function isSticky(): bool
    {
        return $this->sticky;
    }
}
