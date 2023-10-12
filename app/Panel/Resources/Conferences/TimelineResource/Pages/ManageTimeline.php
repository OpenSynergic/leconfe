<?php

namespace App\Panel\Resources\Conferences\TimelineResource\Pages;

use App\Models\Role;
use Filament\Actions;
use App\Models\Timeline;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use App\Panel\Resources\Conferences\TimelineResource;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;

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
                })
        ];
    }
}
