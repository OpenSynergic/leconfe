<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Register extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'register';
    }

    public function getLabel(): string
    {
        return 'Register';
    }

    public function getIsDisplayed(NavigationMenuItem $navigationMenuItem): bool
    {
        return !auth()->check();
    }
}