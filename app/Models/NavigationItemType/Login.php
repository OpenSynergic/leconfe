<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Login extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'login';
    }

    public function getLabel(): string
    {
        return 'Login';
    }
    
    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return route('livewirePageGroup.website.pages.login');
    }

    public function getIsDisplayed(NavigationMenuItem $navigationMenuItem): bool
    {
        return !auth()->check();
    }
}