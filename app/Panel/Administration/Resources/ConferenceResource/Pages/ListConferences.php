<?php

namespace App\Panel\Administration\Resources\ConferenceResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Support\Enums\MaxWidth;
use App\Actions\Series\SerieCreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Actions\Conferences\ConferenceCreateAction;
use App\Models\Enums\SerieState;
use App\Panel\Administration\Resources\ConferenceResource;

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
            Actions\CreateAction::make()
                ->using(function (array $data) {
                    
                    $record = ConferenceCreateAction::run($data);
                    $serie = SerieCreateAction::run([
                        'conference_id' => $record->getKey(),
                        'path' => Str::slug($data['serie']['title']),
                        'state' => SerieState::Current,
                        ...$data['serie'],
                    ]);


                    return $record;
                })
        ];
    }
}
