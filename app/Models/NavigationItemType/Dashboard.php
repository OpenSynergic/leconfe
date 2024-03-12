<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Dashboard extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'dashboard';
    }

    public function getLabel(): string
    {
        return 'Dashboard';
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        $conference = app()->getCurrentConference();

        return route('filament.panel.pages.dashboard', $conference);
    }
}