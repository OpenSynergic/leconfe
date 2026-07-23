<?php

namespace App\Panel\Conference\Resources\UserResource\Pages;

use App\Actions\User\UserCreateAction;
use App\Panel\Conference\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return UserCreateAction::run($data);
    }

    public function getMaxWidth(): string
    {
        return 'full';
    }
}
