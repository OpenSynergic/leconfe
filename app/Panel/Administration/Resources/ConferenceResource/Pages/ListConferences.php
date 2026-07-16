<?php

namespace App\Panel\Administration\Resources\ConferenceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use App\Actions\Conferences\ConferenceCreateAction;
use App\Panel\Administration\Resources\ConferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConferences extends ListRecords
{
    protected static string $resource = ConferenceResource::class;

    public int $upcomingConferenceCount = 0;

    public int $archivedConferenceCount = 0;

    public function mount(): void
    {
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::ExtraLarge)
                ->using(function (array $data) {
                    $record = ConferenceCreateAction::run($data);

                    return $record;
                }),
        ];
    }
}
