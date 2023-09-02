<?php

namespace App\Forms\Components\VerticalTabs;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Tabs\Tab as TabsTab;
use Illuminate\Support\Str;

class Tab extends TabsTab
{
    protected string $view = 'forms.components.vertical-tabs.tab';

    protected bool $sticky = false;

    public function isSticky(): bool
    {
        return $this->sticky;
    }

    public function sticky(bool $sticky = true): static
    {
        $this->sticky = $sticky;

        return $this;
    }
}
