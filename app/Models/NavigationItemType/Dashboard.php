<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class Dashboard extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'dashboard';
    }

    public static function getLabel(): string
    {
        return 'Dashboard';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        $conference = app()->getCurrentConference();

        return $conference ? route('filament.panel.pages.dashboard', ['conference' => $conference]) : route('filament.administration.pages.dashboard');
    }

    public static function getIsDisplayed(NavigationMenuItem $navigationMenuItem): bool
    {
        return auth()->check();
    }
}