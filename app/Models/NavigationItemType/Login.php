<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Login extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'login';
    }

    public static function getLabel(): string
    {
        return 'Login';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return app()->getCurrentConferenceId() ? route('livewirePageGroup.conference.pages.login') : route('livewirePageGroup.website.pages.login');
    }

    public static function getIsDisplayed(NavigationMenuItem $navigationMenuItem): bool
    {
        return ! auth()->check();
    }
}
