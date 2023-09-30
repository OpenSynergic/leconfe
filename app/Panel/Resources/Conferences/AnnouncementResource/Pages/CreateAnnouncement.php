<?php

namespace App\Panel\Resources\Conferences\AnnouncementResource\Pages;

use App\Actions\Announcements\AnnouncementCreateAction;
use App\Panel\Resources\Conferences\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return AnnouncementCreateAction::run($data, data_get($data, 'send_email', false));
    }
}
