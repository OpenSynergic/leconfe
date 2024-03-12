<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;

abstract class BaseNavigationItemType
{
    abstract public function getId(): string;  

    abstract public function getLabel(): string;

    public function getDescription(NavigationMenuItem $navigationMenuItem): ?string
    {
        return null;
    }

    public function getIsDisplayed(NavigationMenuItem $navigationMenuItem): bool
    {
        return true;
    }

    public function getAdditionalForm(NavigationMenuItem $navigationMenuItem): array
    {
        return [];
    }
    
    public function getAdditionalFormData(NavigationMenuItem $navigationMenuItem): array
    {
        return [];
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return '#';
    }
}
