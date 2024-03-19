<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Announcements extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'announcements';
    }

    public static function getLabel(): string
    {
        return 'Announcements';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        $conference = app()->getCurrentConference();

        return $conference ? route('livewirePageGroup.conference.pages.announcement-list', ['conference' => $conference]) : '#';
    }
}
