<?php

namespace App\Panel\Conference\Resources\ProceedingResource\Pages;

use App\Panel\Conference\Resources\ProceedingResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageProceedings extends ManageRecords
{
    protected static string $resource = ProceedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'future' => Tab::make()
                ->label('Future Proceedings')
                ->modifyQueryUsing(fn (Builder $query) => $query->published(false)),
            'back' => Tab::make()
                ->label('Back Proceedings')
                ->modifyQueryUsing(fn (Builder $query) => $query->published()),
        ];
    }
}
