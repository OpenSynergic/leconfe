<?php

namespace App\Panel\Series\Resources\SpeakerResource\Pages;

use App\Panel\Series\Resources\SpeakerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSpeakers extends ManageRecords
{
    protected static string $view = 'panel.series.resources.speaker-resource.pages.list-speakers';

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
