<?php

namespace App\Panel\Resources\Conferences\SpeakerResource\Pages;

use App\Panel\Resources\Conferences\SpeakerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSpeakers extends ManageRecords
{
    protected static string $view = 'panel.resources.conferences.speaker-resource.pages.list-speakers';

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
