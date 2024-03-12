<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Home extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'home';
    }

    public function getLabel(): string
    {
        return 'Home';
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return route('livewirePageGroup.website.pages.home');
    }
}