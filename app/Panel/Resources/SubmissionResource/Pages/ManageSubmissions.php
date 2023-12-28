<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use App\Panel\Pages\Settings\Workflow;
use App\Panel\Resources\SubmissionResource;
use Awcodes\Shout\Components\ShoutEntry;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ManageSubmissions extends ManageRecords
{
    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.list-submission';

    protected const TAB_MYQUEUE = 'My Queue';

    protected const TAB_ACTIVE = 'Active';

    protected const TAB_PUBLISHED = 'Published';

    protected const TAB_ARCHIVED = 'Archived';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ShoutEntry::make('title')
                    ->hidden(function () {
                        return StageManager::callForAbstract()->isStageOpen() || ! Auth::user()->can('Workflow:update');
                    })
                    ->type('warning')
                    ->content(function () {
                        $htmlString = 'Call for abstract stage is closed. ';
                        $htmlString .= sprintf("<a href='%s' class='text-warning-700 hover:underline'>Click here</a> to open it.", Workflow::getUrl());

                        return new HtmlString($htmlString);
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Settings')
                ->button()
                ->authorize('Workflow:update')
                ->outlined()
                ->icon('heroicon-o-cog')
                ->url(Workflow::getUrl()),
            Action::make('create')
                ->button()
                ->authorize('Submission:create')
                ->disabled(
                    fn (): bool => !StageManager::callForAbstract()->isStageOpen()
                )
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

    protected static function generateQueryByCurrentUser(string $tabs)
    {
        $statuses = match ($tabs) {
            static::TAB_MYQUEUE => [
                SubmissionStatus::Queued,
            ],
            static::TAB_ACTIVE => [
                SubmissionStatus::OnReview,
                SubmissionStatus::Editing,
            ],
            static::TAB_PUBLISHED => [
                SubmissionStatus::Published,
            ],
            static::TAB_ARCHIVED => [
                SubmissionStatus::Declined,
                SubmissionStatus::Withdrawn,
            ],
            default => null,
        };

        $query = static::getResource()::getEloquentQuery();

        if (Auth::user()->hasAnyRole([
            UserRole::Admin->value,
            UserRole::ConferenceManager->value
        ])) {
            return $query->whereIn('status', $statuses);
        }

        return $query->when(
            Auth::user()->hasRole(UserRole::Author->value),
            function (Builder $query) {
                $query->where('user_id', Auth::id());
            }
        )->when(
            Auth::user()->hasRole(UserRole::Reviewer->value),
            function (Builder $query) use ($statuses) {
                $query->whereHas('reviews', function (Builder $query) {
                    return $query->where('user_id', Auth::id());
                })->whereIn('status', [...$statuses, SubmissionStatus::OnReview]);
            }
        )->when(
            Auth::user()->hasRole(UserRole::Editor->value),
            function (Builder $query) use ($statuses) {
                $query->whereHas('participants', function (Builder $query) {
                    return $query->where('user_id', Auth::id());
                })->whereIn('status', $statuses);
            }
        );
    }

    /** Need to be optimized */
    public function getTabs(): array
    {

        return [
            static::TAB_MYQUEUE => Tab::make("My Queue")
                ->modifyQueryUsing(fn (): Builder => static::generateQueryByCurrentUser(static::TAB_MYQUEUE)),
            static::TAB_ACTIVE => Tab::make("Active")
                ->modifyQueryUsing(fn (): Builder => static::generateQueryByCurrentUser(static::TAB_ACTIVE)),
            static::TAB_PUBLISHED => Tab::make("Published")
                ->modifyQueryUsing(fn (): Builder => static::generateQueryByCurrentUser(static::TAB_PUBLISHED)),
            static::TAB_ARCHIVED => Tab::make("Archived")
                ->modifyQueryUsing(fn (): Builder => static::generateQueryByCurrentUser(static::TAB_ARCHIVED)),
        ];
    }
}
