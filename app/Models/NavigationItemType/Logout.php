<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Logout extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'logout';
    }

    public function getLabel(): string
    {
        return 'Logout';
    }

    public function getIsDisplayed(NavigationMenuItem $navigationMenuItem): bool
    {
        return auth()->check();
    }
}