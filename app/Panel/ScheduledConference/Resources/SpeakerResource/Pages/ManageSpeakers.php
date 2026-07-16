<?php

namespace App\Panel\ScheduledConference\Resources\SpeakerResource\Pages;

use App\Panel\ScheduledConference\Resources\SpeakerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSpeakers extends ManageRecords
{
    protected string $view = 'panel.scheduledConference.resources.speaker-resource.pages.list-speakers';

    protected static string $resource = SpeakerResource::class;

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
