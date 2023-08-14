<?php

namespace App\Panel\Resources\Conferences\TopicResource\Pages;

use App\Panel\Resources\Conferences\TopicResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTopics extends ManageRecords
{
    protected static string $resource = TopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
