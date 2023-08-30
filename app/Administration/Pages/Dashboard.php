<?php

namespace App\Administration\Pages;

use Filament\Pages\Dashboard as Page;

class Dashboard extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $title = 'Administration';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'administration.pages.dashboard';
}
