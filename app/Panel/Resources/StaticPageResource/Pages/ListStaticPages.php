<?php

namespace App\Panel\Resources\StaticPageResource\Pages;

use App\Panel\Resources\StaticPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaticPages extends ListRecords
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
