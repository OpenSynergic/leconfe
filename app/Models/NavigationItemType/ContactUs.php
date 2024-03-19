<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

class ContactUs extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'contact-us';
    }

    public static function getLabel(): string
    {
        return 'Contact Us';
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return '#';
    }
}