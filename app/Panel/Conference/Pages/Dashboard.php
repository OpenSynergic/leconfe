<?php

namespace App\Panel\Conference\Pages;

use App\Facades\Settings;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    public function mount()
    {
        return dd(Settings::all());
    }
}
