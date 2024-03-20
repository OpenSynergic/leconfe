<?php

namespace App\Panel\Administration\Resources\ConferenceResource\Pages;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Panel\Administration\Resources\ConferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditConference extends EditRecord
{
    protected static string $resource = ConferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            ...$data,
            'meta' => $this->getRecord()->getAllMeta()->toArray(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        ConferenceUpdateAction::run($record, $data);

        return $record;
    }
}
