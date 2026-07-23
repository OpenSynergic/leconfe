<?php

namespace App\Panel\ScheduledConference\Resources\AnnouncementResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use App\Actions\Announcements\AnnouncementCreateAction;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Panel\ScheduledConference\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::TwoExtraLarge)
                ->using(fn (array $data) => AnnouncementCreateAction::run($data, $data['send_email'] ?? false)),
        ];
    }
}
