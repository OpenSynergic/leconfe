<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Announcements extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'announcements';
    }

    public function getLabel(): string
    {
        return 'Announcements';
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return route('livewirePageGroup.current-conference.pages.announcement-list');
    }
}