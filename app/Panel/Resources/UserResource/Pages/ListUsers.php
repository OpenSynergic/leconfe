<?php

namespace App\Panel\Resources\UserResource\Pages;

use Filament\Actions;
use App\Panel\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use App\Panel\Resources\UserResource\Widgets\UserOverview;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserOverview::class,
        ];
    }
}
