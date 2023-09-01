<?php

namespace App\Panel\Resources\UserResource\Pages;

use App\Actions\User\UserCreateAction;
use App\Panel\Resources\UserResource;
use Filament\Actions;
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
        dd($data);
       return UserCreateAction::run($data);
    }
}
