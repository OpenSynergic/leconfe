<?php

namespace App\Panel\Conference\Resources\PresenterResource\Pages;

use App\Panel\Conference\Resources\PresenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPresenter extends EditRecord
{
    protected static string $resource = PresenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
