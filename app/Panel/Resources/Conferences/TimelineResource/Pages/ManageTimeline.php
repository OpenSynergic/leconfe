<?php

namespace App\Panel\Resources\Conferences\TimelineResource\Pages;

use Filament\Actions;
use App\Models\Timeline;
use Filament\Resources\Pages\ManageRecords;
use App\Panel\Resources\Conferences\TimelineResource;

class ManageTimeline extends ManageRecords
{
    protected static string $resource = TimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading('Add Timeline')
                ->mutateFormDataUsing(function (array $data) {
                    $dateFormat = date('Y-m-d', strtotime($data['date']));
                    $data['date'] = $dateFormat;
                    return $data;
                })
        ];
    }
}
