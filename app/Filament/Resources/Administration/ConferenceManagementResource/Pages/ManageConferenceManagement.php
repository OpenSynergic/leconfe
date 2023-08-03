<?php

namespace App\Filament\Resources\Administration\ConferenceManagementResource\Pages;

use App\Actions\Conferences\ConferenceCreateAction;
use App\Filament\Resources\Administration\ConferenceManagementResource;
use App\Models\Conference;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConferenceManagement extends ManageRecords
{
    protected static string $resource = ConferenceManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('2xl')
                ->using(fn (array $data): Conference => ConferenceCreateAction::run($data)),
        ];
    }
}
