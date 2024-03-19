<?php

namespace App\Panel\Conference\Resources\UserResource\Pages;

use App\Actions\User\CreateParticipantFromUserAction;
use App\Actions\User\UserCreateAction;
use App\Panel\Conference\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function afterCreate(): void
    {
        CreateParticipantFromUserAction::run($this->getRecord());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return UserCreateAction::run($data);
    }
}
