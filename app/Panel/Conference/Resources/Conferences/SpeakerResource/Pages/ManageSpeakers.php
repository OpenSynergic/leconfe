<?php

namespace App\Panel\Conference\Resources\Conferences\SpeakerResource\Pages;

use App\Panel\Conference\Resources\Conferences\SpeakerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSpeakers extends ManageRecords
{
    protected static string $view = 'panel.conference.resources.conferences.speaker-resource.pages.list-speakers';

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
