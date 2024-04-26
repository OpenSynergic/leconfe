<?php

namespace App\Panel\Conference\Pages;

use App\Facades\Settings;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    public function mount()
    {
        $settings = Settings::all();
        // dd($settings);
        // dd($settings['mail_auth_port'] ?? null);
    }
}
