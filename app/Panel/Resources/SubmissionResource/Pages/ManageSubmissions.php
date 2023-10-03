<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Models\Conference;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Pages\Settings\Workflow;
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
            Action::make('Settings')
                ->button()
                ->outlined()
                ->icon("heroicon-o-cog")
                ->url(Workflow::getUrl()),
            Action::make('create')
                ->button()
                ->disabled(function (): bool {
                    $conference = Conference::active();
                    return !$conference->getMeta('workflow.call-for-abstract.open', false);
                })
                ->url(static::$resource::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->label(function (Action $action) {
                    if ($action->isDisabled()) {
                        return 'Submission is not open';
                    }
                    return 'Submission';
                }),
        ];
    }

    public function getTabs(): array
    {
        // ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Submission::STATUS_ACTIVE))

        return [
            'new' => Tab::make('New')
                ->badge(
                    fn () => Submission::query()
                        ->count()
                ),
            'review' => Tab::make('Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', SubmissionStatus::UnderReview))
                ->badge(
                    fn () => Submission::query()
                        ->where('status', SubmissionStatus::UnderReview)
                        ->count()
                ),
            'archived' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', SubmissionStatus::Published))
                ->badge(
                    fn () => Submission::query()
                        ->where('status', SubmissionStatus::Published)
                        ->count()
                ),
        ];
    }
}
