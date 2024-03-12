<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Proceedings extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'proceedings';
    }

    public function getLabel(): string
    {
        return 'Proceedings';
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return route('livewirePageGroup.current-conference.pages.proceeding');
    }
}