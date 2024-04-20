<?php

namespace App\Panel\Conference\Pages;

use App\Facades\Settings;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    public function mount()
    {
        Settings::set('test', 'gdgdg');
        return dd(Settings::get('test'));
    }
}
