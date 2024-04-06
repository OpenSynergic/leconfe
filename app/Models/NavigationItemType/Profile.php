<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;
use App\Panel\Conference\Pages\Profile as ConferenceProfile;

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
        return app()->getCurrentConferenceId() ? route('filament.conference.pages.profile') : route('filament.administration.pages.profile');
    }
}
