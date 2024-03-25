<?php

namespace App\Panel\Conference\Resources\PresenterResource\Pages;

use App\Panel\Conference\Resources\PresenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ManageRecords;

class ManagePresenters extends ManageRecords
{
    protected static string $resource = PresenterResource::class;
}
