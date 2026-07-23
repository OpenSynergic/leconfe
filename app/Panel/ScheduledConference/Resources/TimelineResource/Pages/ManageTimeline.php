<?php

namespace App\Panel\ScheduledConference\Resources\TimelineResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use App\Models\Timeline;
use App\Panel\ScheduledConference\Resources\TimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ManageTimeline extends ListRecords
{
    protected static string $resource = TimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading(__('general.add_timeline'))
                ->modalWidth(Width::ExtraLarge)
                ->model(Timeline::class)
                ->authorize('create', Timeline::class),
        ];
    }
}
