<?php

namespace App\Administration\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class Dashboard extends Page
{

    protected static ?string $navigationIcon = 'heroicon-m-home';

    protected static ?string $title = 'Administration';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'administration.pages.dashboard';
}