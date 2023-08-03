<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Filament\Resources\SubmissionResource;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class ManageSubmissions extends ManageRecords
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('create')
            //     ->,
            Action::make('create')
                ->button()
                ->hidden(fn () => setting('disable_submission'))
                ->url(static::$resource::getUrl('create'))
                ->label('New Submission')
        ];
    }


    public function getTabs(): array
    {
        return [
            'new' => Tab::make('New')
            // ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Submission::STATUS_ACTIVE))
            ,
            'review' => Tab::make('Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Submission::STATUS_REVIEW)),
            'archived' => Tab::make('Archived')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Submission::STATUS_PUBLISHED)),
        ];
    }
}
