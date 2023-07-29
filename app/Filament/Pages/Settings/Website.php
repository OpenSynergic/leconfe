<?php

namespace App\Filament\Pages\Settings;

use Filament\Pages\Page;

class Website extends Page
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-globe';

    protected static string $view = 'filament.pages.settings.website';
}
