<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Home extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'home';
    }

    public static function getLabel(): string
    {
        return 'Home';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        $conference = app()->getCurrentConference();
        return $conference ? route('livewirePageGroup.conference.pages.home', ['conference' => $conference]) : route('livewirePageGroup.website.pages.home');
    }
}