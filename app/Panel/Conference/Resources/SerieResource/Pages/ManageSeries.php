<?php

namespace App\Panel\Conference\Resources\SerieResource\Pages;

use App\Actions\Series\SerieCreateAction;
use App\Panel\Conference\Resources\SerieResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageSeries extends ManageRecords
{
    protected static string $resource = SerieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn(array $data) => SerieCreateAction::run($data)),
        ];
    }

    public function getTabs(): array
    {
        return [
            'current' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('current', true)),
            'future' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('published', false)),
            'published' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('published', true)),
            // 'trash' => Tab::make()
            //     ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
