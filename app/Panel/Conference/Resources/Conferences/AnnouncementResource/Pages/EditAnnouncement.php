<?php

namespace App\Panel\Conference\Resources\Conferences\AnnouncementResource\Pages;

use App\Actions\Announcements\AnnouncementUpdateAction;
use App\Panel\Conference\Resources\Conferences\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view')
                ->icon('heroicon-o-eye')
                ->label('View as page')
                ->color('success')
                ->url(function ($record) {
                    $conference = $record->conference;

                    return route('livewirePageGroup.conference.pages.announcement-page', [
                        'conference' => $conference->path,
                        'announcement' => $record->id,
                    ]);
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return AnnouncementUpdateAction::run($data, $record);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        $data['author'] = $user ? $user->full_name : 'Cannot find the author';
        $data['common_tags'] = $this->record->tags()->pluck('id')->toArray();
        $data['meta'] = $this->record->getAllMeta();

        return $data;
    }
}
