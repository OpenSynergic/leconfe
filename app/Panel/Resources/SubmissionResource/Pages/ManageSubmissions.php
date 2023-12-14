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
use Illuminate\Support\Facades\Auth;

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

    /** Need to be optimized */
    public function getTabs(): array
    {
        return [
            'My Queue' => Tab::make('My Queue')
                ->when(
                    Auth::user()->hasRole(UserRole::Author->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->where('user_id', Auth::id())
                                    ->inAnyStatus([
                                        SubmissionStatus::Queued,
                                        SubmissionStatus::OnReview,
                                    ]);
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas('reviews', function (Builder $query) {
                                    return $query->where('user_id', Auth::id());
                                })->inAnyStatus([
                                    SubmissionStatus::Incomplete,
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ]);
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Editor->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas('participants', function (Builder $query) {
                                    return $query->where('user_id', Auth::id());
                                })->inAnyStatus([
                                    SubmissionStatus::Incomplete,
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ]);
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasAnyRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->inAnyStatus([
                                    SubmissionStatus::Incomplete,
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ]);
                            }
                        );
                    }
                ),
            'Active' => Tab::make('Active')
                ->when(
                    Auth::user()->hasRole(UserRole::Author->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->where('user_id', Auth::id())
                                    ->inAnyStatus([
                                        SubmissionStatus::Queued,
                                        SubmissionStatus::OnReview,
                                        SubmissionStatus::Editing,
                                    ]);
                            }

                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas('reviews', function (Builder $query) {
                                    return $query->where('user_id', Auth::id());
                                })->inAnyStatus([
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ]);
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Editor->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas(
                                    'participants',
                                    function (Builder $query) {
                                        return $query->where('user_id', Auth::id());
                                    }
                                )
                                    ->inAnyStatus([
                                        SubmissionStatus::Queued,
                                        SubmissionStatus::OnReview,
                                        SubmissionStatus::Editing,
                                    ]);
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasAnyRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->inAnyStatus([
                                    SubmissionStatus::Queued,
                                    SubmissionStatus::OnReview,
                                    SubmissionStatus::Editing,
                                ]);
                            }
                        );
                    }
                ),
            'Published' => Tab::make('Published')
                ->when(
                    Auth::user()->hasRole(UserRole::Author->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->where('user_id', Auth::id())
                                    ->published();
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Editor->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas('participants', function (Builder $query) {
                                    return $query->where('user_id', Auth::id());
                                })->published();
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas('reviews', function (Builder $query) {
                                    return $query->where('user_id', Auth::id());
                                })->published();
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->published();
                            }
                        );
                    }
                ),
            'Archived' => Tab::make('Archived')
                ->when(
                    Auth::user()->hasRole(UserRole::Author->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->where('user_id', Auth::id())
                                    ->inAnyStatus([
                                        SubmissionStatus::Declined,
                                        SubmissionStatus::Withdrawn,
                                    ]);
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Editor->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas('participants', function (Builder $query) {
                                    return $query->where('user_id', Auth::id());
                                })->inAnyStatus([
                                    SubmissionStatus::Declined,
                                    SubmissionStatus::Withdrawn,
                                ]);
                            }
                        );
                    }
                )
                ->when(
                    Auth::user()->hasRole(UserRole::Reviewer->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->whereHas('reviews', function (Builder $query) {
                                    return $query->where('user_id', Auth::id());
                                })->inAnyStatus([
                                    SubmissionStatus::Declined,
                                    SubmissionStatus::Withdrawn,
                                ]);
                            }

                        );
                    }
                )
                ->when(
                    Auth::user()->hasAnyRole([
                        UserRole::Admin->value,
                        UserRole::ConferenceManager->value
                    ]),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(
                            function (Builder $query) {
                                return $query->inAnyStatus([
                                    SubmissionStatus::Declined,
                                    SubmissionStatus::Withdrawn,
                                ]);
                            }
                        );
                    }
                )
        ];
    }
}
