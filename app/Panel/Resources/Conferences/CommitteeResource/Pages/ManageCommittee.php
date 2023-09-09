<?php

namespace App\Panel\Resources\Conferences\CommitteeResource\Pages;

use App\Panel\Resources\Conferences\CommitteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommittee extends ManageRecords
{
    protected static string $view = 'panel.resources.conferences.committee-resource.pages.list-committees';

    protected static string $resource = CommitteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return static::getResource()::getWidgets();
    }
}
