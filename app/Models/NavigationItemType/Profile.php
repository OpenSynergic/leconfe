<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Profile extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'profile';
    }

    public static function getLabel(): string
    {
        return 'Profile';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return route('filament.panel.resources.users.profile', [
            'tenant' => app()->getCurrentConference(),
        ]); 
    }
}