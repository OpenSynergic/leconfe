<?php

namespace App\Panel\Resources\Conferences\AnnouncementResource\Pages;

use App\Actions\Announcements\AnnouncementUpdateAction;
use App\Models\Enums\ConferenceStatus;
use App\Panel\Resources\Conferences\AnnouncementResource;
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

                    switch ($conference->status->value) {
                        case ConferenceStatus::Current->value:
                            return
                                route('livewirePageGroup.current-conference.pages.announcement-page', [
                                    'id' => $record->id,
                                ]);
                            break;

                        default:
                            return
                                route('livewirePageGroup.archive-conference.pages.announcement-page', [
                                    'conference' => $conference->id,
                                    'id' => $record->id,
                                ]);
                            break;
                    }
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return AnnouncementUpdateAction::run($data, $record);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $userContentMeta = $this->record->getAllMeta();
        $user = $this->record->user;

        $data['author'] = $user ? "{$user->given_name} {$user->family_name}" : 'Cannot find the author';
        $data['common_tags'] = $this->record->tags()->pluck('id')->toArray();
        $data['user_content'] = $userContentMeta['user_content'];
        $data['expires_at'] = $userContentMeta['expires_at'];

        return $data;
    }
}
