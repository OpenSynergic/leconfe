<?php

namespace App\Panel\Resources\UserResource\Pages;

use App\Panel\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Pages\Page;

class ProfileUser extends Page
{
    protected static string $resource = UserResource::class;

    
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        if (! $this->hasInfolist()) {
            $this->fillForm();
        }
    }
}
