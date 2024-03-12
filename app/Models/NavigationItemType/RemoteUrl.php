<?php

namespace App\Models\NavigationItemType;

use App\Models\NavigationMenuItem;
use Filament\Forms\Components\TextInput;

class RemoteUrl extends BaseNavigationItemType
{
    public function getId(): string
    {
        return 'remote-url';
    }

    public function getLabel(): string
    {
        return 'Remote URL';
    }

    public function getAdditionalForm(NavigationMenuItem $navigationMenuItem): array
    {
        return [
            TextInput::make('meta.url')
                ->label('URL')
                ->url()
                ->required()
                ->placeholder('https://example.com'),
        ];
    }

    public function getAdditionalFormData(NavigationMenuItem $navigationMenuItem): array
    {
        return [
            'meta' => [
                'url' => $navigationMenuItem->getMeta('url'),
            ],
        ];
    }

    public function getUrl(NavigationMenuItem $navigationMenuItem): string
    {
        return $navigationMenuItem->getMeta('url');
    }
}