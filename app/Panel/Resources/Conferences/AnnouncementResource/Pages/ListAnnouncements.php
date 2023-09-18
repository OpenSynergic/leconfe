<?php

namespace App\Panel\Resources\Conferences\AnnouncementResource\Pages;

use App\Actions\Announcements\AnnouncementCreateAction;
use App\Models\Enums\ContentType;
use App\Panel\Resources\Conferences\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn (array $data) => AnnouncementCreateAction::run($data, $data['send_email'] ?? false))
                ->mutateFormDataUsing(function ($data) {
                    $data['content_type'] = ContentType::Announcement;

                    return $data;
                }),
            // ->using(fn(array $data) => dd($data)),
        ];
    }
}
