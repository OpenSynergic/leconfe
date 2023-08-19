<?php

namespace App\Panel\Resources\Concern;

trait HasNavigationBadge
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getNavigationBadge() ? 'primary' : 'gray';
    }
}
