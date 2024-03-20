<?php

namespace App\Panel\Conference\Resources\Conferences\TimelineResource\Pages;

use App\Panel\Conference\Resources\Conferences\TimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTimeline extends ManageRecords
{
    protected static string $resource = TimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading('Add Timeline')
                ->form(fn () => TimelineResource::formSchemas())
                ->mutateFormDataUsing(function (array $data) {
                    $dateFormat = date('Y-m-d', strtotime($data['date']));
                    $data['date'] = $dateFormat;

                    return $data;
                }),
        ];
    }
}
