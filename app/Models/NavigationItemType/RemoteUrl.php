<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;
use Filament\Forms\Components\TextInput;

class RemoteUrl extends BaseNavigationItemType
{
    public static function getId(): string
    {
        return 'remote-url';
    }

    public static function getLabel(): string
    {
        return 'Remote URL';
    }

    public static function getAdditionalForm(): array
    {
        return [
            TextInput::make('meta.url')
                ->label('URL')
                ->url()
                ->required()
                ->placeholder('https://example.com'),
        ];
    }

    public static function getAdditionalFormData(NavigationMenuItem $navigationMenuItem): array
    {
        return [
            'meta' => [
                'url' => $navigationMenuItem->getMeta('url'),
            ],
        ];
    }

    public static function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return $navigationMenuItem->getMeta('url');
    }
}
