<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Proceedings extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'proceedings';
    }

    public static function getLabel(): string
    {
        return 'Proceedings';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return app()->getCurrentConferenceId() ? route('livewirePageGroup.conference.pages.proceedings') : '#';
    }
}
