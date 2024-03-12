<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class About extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'about';
    }

    public function getLabel(): string
    {
        return 'About';
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return route('livewirePageGroup.current-conference.pages.about');
    }
}