<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Home extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'home';
    }

    public static function getLabel(): string
    {
        return 'Home';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return app()->getCurrentConferenceId() ? route('livewirePageGroup.conference.pages.home') : route('livewirePageGroup.website.pages.home');
    }
}
