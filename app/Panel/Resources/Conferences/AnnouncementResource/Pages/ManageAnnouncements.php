<?php

namespace App\Panel\Resources\Conferences\AnnouncementResource\Pages;

use App\Actions\UserContents\UserContentCreateAction;
use App\Panel\Resources\Conferences\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAnnouncements extends ManageRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn (array $data) => UserContentCreateAction::run($data, $data['send_email'])),
            // ->using(fn(array $data) => dd($data)),
        ];
    }
}
