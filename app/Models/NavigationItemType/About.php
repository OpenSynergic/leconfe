<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class About extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'about';
    }

    public static function getLabel(): string
    {
        return 'About';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        $conference = app()->getCurrentConference();

        return $conference ? route('livewirePageGroup.conference.pages.about', ['conference' => $conference]) : '#';
    }
}
