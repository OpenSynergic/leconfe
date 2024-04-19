<?php

namespace App\Panel\Conference\Resources\UserResource\Pages;

use App\Actions\User\CreateParticipantFromUserAction;
use App\Actions\User\UserUpdateAction;
use App\Panel\Conference\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function afterSave(): void
    {
        // CreateParticipantFromUserAction::run($this->getRecord());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return UserUpdateAction::run($record, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['meta'] = $this->getRecord()->getAllMeta()->toArray();

        return $data;
    }
}
