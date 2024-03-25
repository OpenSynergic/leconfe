<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class About extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'about';
    }

    public static function getLabel(): string
    {
        return 'About';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
          return app()->getCurrentConferenceId() ? route('livewirePageGroup.conference.pages.about') : '#';
    }
}
