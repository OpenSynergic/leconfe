<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Logout extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'logout';
    }

    public static function getLabel(): string
    {
        return 'Logout';
    }

    public static function getIsDisplayed(NavigationMenuItem $navigationMenuItem): bool
    {
        return auth()->check();
    }
}
