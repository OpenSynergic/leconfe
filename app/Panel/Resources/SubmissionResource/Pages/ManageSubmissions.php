<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use App\Panel\Pages\Settings\Workflow;
use App\Panel\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
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
                ->authorize('WorkflowSetting:update')
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

    public function getTabs(): array
    {
        $currentUser = auth()->user();
        return [
            'My Queue' => Tab::make('My Queue')
                ->when(
                    $currentUser->hasRole(UserRole::Author->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->where('user_id', $currentUser->getKey())
                                ->inAnyStatus([
                                    SubmissionStatus::Incomplete,
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ])
                        );
                    }
                )
                ->when(
                    $currentUser->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->whereHas(
                                'reviews',
                                fn (Builder $query) => $query->where('user_id', $currentUser->getKey())
                            )
                                ->inAnyStatus([
                                    SubmissionStatus::Incomplete,
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ])
                        );
                    }
                )
                ->when(
                    $currentUser->hasRole(UserRole::Editor->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->whereHas(
                                'participants',
                                fn (Builder $query) => $query->where('user_id', $currentUser->getKey())
                            )
                                ->inAnyStatus([
                                    SubmissionStatus::Incomplete,
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ])
                        );
                    }
                )
                ->when(
                    $currentUser->hasAnyRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->inAnyStatus([
                                SubmissionStatus::Incomplete,
                                SubmissionStatus::Queued,
                                SubmissionStatus::OnReview,
                                SubmissionStatus::Editing,
                            ])
                        );
                    }
                ),
            'Active' => Tab::make('Active')
                ->when(
                    $currentUser->hasRole(UserRole::Author->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->where('user_id', auth()->id())
                                ->inAnyStatus([
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ])
                        );
                    }
                )
                ->when(
                    $currentUser->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->whereHas(
                                'reviews',
                                fn (Builder $query) => $query->where('user_id', $currentUser->getKey())
                                    ->inAnyStatus([
                                        SubmissionStatus::Queued,
                                        SubmissionStatus::OnReview,
                                        SubmissionStatus::Editing,
                                    ])
                            )
                        );
                    }
                )
                ->when(
                    $currentUser->hasRole(UserRole::Editor->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->whereHas(
                                'participants',
                                fn (Builder $query) => $query->where('user_id', $currentUser->getKey())
                            )
                                ->inAnyStatus([
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ])
                        );
                    }
                )
                ->when(
                    $currentUser->hasAnyRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query
                                ->inAnyStatus([
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ])
                        );
                    }
                ),
            'Published' => Tab::make('Published')
                ->when(
                    $currentUser->hasRole(UserRole::Author->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query
                                ->where('user_id', $currentUser->getKey())
                                ->published()
                        );
                    }
                )
                ->when(
                    $currentUser->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->whereHas(
                                'reviews',
                                fn (Builder $query) => $query->where('user_id', $currentUser->getKey())
                            )->published()
                        );
                    }
                )
                ->when(
                    $currentUser->hasRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->published()
                        );
                    }
                ),
            'Archived' => Tab::make('Archived')
                ->when(
                    $currentUser->hasRole(UserRole::Author->value),
                    function (Tab $tab) use ($currentUser) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->where('user_id', $currentUser->getKey())
                                ->inAnyStatus([
                                    SubmissionStatus::Declined,
                                    SubmissionStatus::Withdrawn,
                                ])
                        );
                    }
                )
                ->when(
                    $currentUser->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->whereHas(
                                'reviews',
                                fn (Builder $query) => $query->where('user_id', auth()->id())
                                    ->inAnyStatus([
                                        SubmissionStatus::Declined,
                                        SubmissionStatus::Withdrawn,
                                    ])
                            )
                        );
                    }
                )
                ->when(
                    $currentUser->hasAnyRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            fn (Builder $query) => $query->inAnyStatus([
                                SubmissionStatus::Declined,
                                SubmissionStatus::Withdrawn,
                            ])
                        );
                    }
                )
        ];
    }
}
