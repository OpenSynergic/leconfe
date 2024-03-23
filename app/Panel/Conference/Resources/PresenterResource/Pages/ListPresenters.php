<?php

namespace App\Panel\Conference\Resources\PresenterResource\Pages;

use App\Panel\Conference\Resources\PresenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresenters extends ListRecords
{
    protected static string $resource = PresenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
