<?php

namespace App\Panel\ScheduledConference\Resources\CommitteeResource\Pages;

use App\Panel\ScheduledConference\Resources\CommitteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommittee extends ManageRecords
{
    protected string $view = 'panel.scheduledConference.resources.committee-resource.pages.list-committees';

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
