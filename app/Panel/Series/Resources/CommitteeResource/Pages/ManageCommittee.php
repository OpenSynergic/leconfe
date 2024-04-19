<?php

namespace App\Panel\Series\Resources\CommitteeResource\Pages;

use App\Panel\Series\Resources\CommitteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommittee extends ManageRecords
{
    protected static string $view = 'panel.series.resources.committee-resource.pages.list-committees';

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
