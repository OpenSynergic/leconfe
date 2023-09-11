<?php

namespace App\Panel\Resources\Conferences\AnnouncementResource\Pages;

use App\Actions\Announcements\AnnouncementUpdateAction;
use App\Models\User;
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
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return AnnouncementUpdateAction::run($data, $record);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $userContentMeta = $this->record->getAllMeta();
        $user = User::where('id', $userContentMeta['author'] ?? 0)->first();

        $data['author'] = $user ? "{$user->given_name} {$user->family_name}" : null;
        $data['common_tags'] = $this->record->tags()->pluck('id')->toArray();
        $data['user_content'] = $userContentMeta['user_content'];
        $data['expires_at'] = $userContentMeta['expires_at'];

        return $data;
    }
}
