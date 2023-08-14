<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Models\Submission;
use App\Panel\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageSubmissions extends ManageRecords
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->button()
                ->hidden(fn () => setting('disable_submission'))
                ->url(static::$resource::getUrl('create'))
                ->label('New Submission'),
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
            'archived' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Submission::STATUS_PUBLISHED)),
        ];
    }
}