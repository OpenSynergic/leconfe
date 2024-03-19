<?php

namespace App\Panel\Conference\Resources\Conferences\AuthorPositionResource\Pages;

use App\Panel\Conference\Resources\Conferences\AuthorPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuthorPositions extends ManageRecords
{
    protected static string $resource = AuthorPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
