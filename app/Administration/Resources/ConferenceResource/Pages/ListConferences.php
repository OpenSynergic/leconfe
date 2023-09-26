<?php

namespace App\Administration\Resources\ConferenceResource\Pages;

use App\Administration\Resources\ConferenceResource;
use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListConferences extends ListRecords
{
    protected static string $resource = ConferenceResource::class;

    public int $upcomingConferenceCount = 0;

    public int $archivedConferenceCount = 0;

    public function mount(): void
    {
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'upcoming' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ConferenceStatus::Upcoming))
                ->badge(Conference::query()->where('status', ConferenceStatus::Upcoming)->count()),
            'active' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ConferenceStatus::Active)),
            'archive' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ConferenceStatus::Archived))
                ->badge(Conference::query()->where('status', ConferenceStatus::Archived)->count()),
        ];
    }
}
