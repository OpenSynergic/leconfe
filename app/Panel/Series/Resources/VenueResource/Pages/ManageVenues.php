<?php

namespace App\Panel\Series\Resources\VenueResource\Pages;

use App\Panel\Series\Resources\VenueResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVenues extends ManageRecords
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }
}
