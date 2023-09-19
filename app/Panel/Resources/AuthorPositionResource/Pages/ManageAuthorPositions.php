<?php

namespace App\Panel\Resources\AuthorPositionResource\Pages;

use App\Panel\Resources\AuthorPositionResource;
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
