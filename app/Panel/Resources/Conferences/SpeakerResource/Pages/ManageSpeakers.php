<?php

namespace App\Panel\Resources\Conferences\SpeakerResource\Pages;

use App\Panel\Resources\Conferences\SpeakerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;

class ManageSpeakers extends ManageRecords
{
    protected static string $resource = SpeakerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // ->form(fn () => dd((app(PersistentMiddleware::class))->getPersistentMiddleware())),
        ];
    }
}
