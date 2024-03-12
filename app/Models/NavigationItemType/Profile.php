<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Profile extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'profile';
    }

    public function getLabel(): string
    {
        return 'Profile';
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return route('filament.panel.resources.users.profile', [
            'tenant' => app()->getCurrentConference(),
        ]); 
    }
}