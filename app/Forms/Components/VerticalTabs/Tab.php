<?php

namespace App\Forms\Components\VerticalTabs;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Tabs\Tab as TabsTab;
use Illuminate\Support\Str;

class Tab extends TabsTab
{
    protected string $view = 'forms.components.vertical-tabs.tab';
}
