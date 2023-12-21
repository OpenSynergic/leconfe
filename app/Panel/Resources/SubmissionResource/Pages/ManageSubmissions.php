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
                    fn (): bool => ! StageManager::callForAbstract()->isStageOpen()
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
        return [
            'My Queue' => Tab::make('My Queue')
                ->when(auth()->user()->hasRole(UserRole::Author->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->where('user_id', auth()->id());
                    });
                })
                ->when(auth()->user()->hasRole(UserRole::Reviewer->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->whereHas(
                            'reviews',
                            fn (Builder $query) => $query->where('user_id', auth()->id())
                        );
                    });
                })
                ->when(auth()->user()->hasRole(UserRole::Editor->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->whereHas(
                            'participants',
                            fn (Builder $query) => $query->where('user_id', auth()->id())
                        );
                    });
                }),
            'Active' => Tab::make('Active')
                ->when(
                    auth()->user()->hasRole(UserRole::Author->value),
                    function (Tab $tab) {
                        return $tab->modifyQueryUsing(function (Builder $query) {
                            return $query->where('user_id', auth()->id());
                        });
                    }
                )
                ->when(auth()->user()->hasRole(UserRole::Reviewer->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->whereHas(
                            'reviews',
                            fn (Builder $query) => $query->where('user_id', auth()->id())
                        );
                    });
                }),
            'Published' => Tab::make('Published')
                ->when(auth()->user()->hasRole(UserRole::Author->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->where('user_id', auth()->id());
                    });
                })
                ->when(auth()->user()->hasRole(UserRole::Reviewer->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->whereHas(
                            'reviews',
                            fn (Builder $query) => $query->where('user_id', auth()->id())
                        );
                    });
                })
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('status', SubmissionStatus::Published);
                }),
            'Declined' => Tab::make('Declined')
                ->when(auth()->user()->hasRole(UserRole::Author->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->where('user_id', auth()->id());
                    });
                })
                ->when(auth()->user()->hasRole(UserRole::Reviewer->value), function (Tab $tab) {
                    return $tab->modifyQueryUsing(function (Builder $query) {
                        return $query->whereHas(
                            'reviews',
                            fn (Builder $query) => $query->where('user_id', auth()->id())
                        );
                    });
                })
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('status', SubmissionStatus::Declined);
                }),
        ];
    }
}
