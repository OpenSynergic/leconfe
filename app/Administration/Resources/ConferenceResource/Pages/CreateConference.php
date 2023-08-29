<?php

namespace App\Administration\Resources\ConferenceResource\Pages;

use App\Actions\Conferences\ConferenceCreateAction;
use App\Administration\Resources\ConferenceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateConference extends CreateRecord
{
    protected static string $resource = ConferenceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return ConferenceCreateAction::run($data);
    }
}
