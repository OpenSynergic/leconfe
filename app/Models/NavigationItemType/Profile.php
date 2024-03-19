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
        $conference = app()->getCurrentConference();

        return $conference ? route('filament.panel.resources.users.profile', [
            'conference' => app()->getCurrentConference(),
        ]) : '#';
    }
}
