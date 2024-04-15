<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Search extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'search';
    }

    public static function getLabel(): string
    {
        return 'Search';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return app()->getCurrentConferenceId() ? '#' : route('livewirePageGroup.website.pages.search');
    }
}
