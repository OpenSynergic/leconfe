<?php

namespace App\Panel\Conference\Resources\SerieResource\Pages;

use App\Panel\Conference\Resources\SerieResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSeries extends ManageRecords
{
    protected static string $resource = SerieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl'),
        ];
    }
}
