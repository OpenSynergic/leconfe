<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Models\Conference;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
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
                ->authorize("WorkflowSetting:update")
                ->outlined()
                ->icon("heroicon-o-cog")
                ->url(Workflow::getUrl()),
            Action::make('create')
                ->button()
                ->authorize("Submission:create")
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
        $currentUser = auth()->user();
        $currentUserAsParticipant = $currentUser->asParticipant();

        $tabs  = [
            'My Submission' => Tab::make("My Submission")
                ->modifyQueryUsing(function (Builder $query) use ($currentUser) {
                    return $query->where('user_id', $currentUser->id);
                }),
        ];

        if ($currentUser->hasRole(UserRole::ConferenceManager->value)) {
            $tabs = [
                ...$tabs,
                SubmissionStage::CallforAbstract->value => Tab::make(SubmissionStage::CallforAbstract->value)
                    ->modifyQueryUsing(
                        function (Builder $query) use ($currentUser) {
                            return $query->stage(SubmissionStage::CallforAbstract);
                        }
                    ),
                SubmissionStage::PeerReview->value => Tab::make(SubmissionStage::PeerReview->value)
                    ->modifyQueryUsing(
                        function (Builder $query) use ($currentUser, $currentUserAsParticipant) {
                            $finalQuery = $query->stage(SubmissionStage::PeerReview);
                            if ($currentUser->hasRole(UserRole::Reviewer->value)) {
                                $finalQuery = $finalQuery
                                    ->whereHas(
                                        'reviews',
                                        fn (Builder $query) => $query->where('participant_id', $currentUserAsParticipant->getKey())
                                    );
                            }
                            return $finalQuery;
                        }
                    ),
                SubmissionStage::Editing->value => Tab::make(SubmissionStage::Editing->value)
                    ->modifyQueryUsing(function (Builder $query) use ($currentUser) {
                        return $query->stage(SubmissionStage::Editing);
                    })
            ];
        } else if ($currentUser->hasRole(UserRole::Reviewer->value)) {
            $tabs = [
                ...$tabs,
                SubmissionStage::PeerReview->value => Tab::make(SubmissionStage::PeerReview->value)
                    ->modifyQueryUsing(
                        function (Builder $query) use ($currentUser, $currentUserAsParticipant) {
                            $finalQuery = $query->stage(SubmissionStage::PeerReview);
                            if ($currentUser->hasRole(UserRole::Reviewer->value)) {
                                $finalQuery = $finalQuery
                                    ->whereHas(
                                        'reviews',
                                        fn (Builder $query) => $query->where('participant_id', $currentUserAsParticipant->getKey())
                                    );
                            }
                            return $finalQuery;
                        }
                    ),
            ];
        }

        if (!$currentUser->hasRole(UserRole::Reviewer->value)) {
            $tabs = [
                ...$tabs,
                'Active' => Tab::make('Active')
                    ->modifyQueryUsing(
                        fn (Builder $query): Builder => $query->where('status', '!=', SubmissionStatus::Declined)
                    ),
                'Published' => Tab::make("Published")
                    ->modifyQueryUsing(
                        fn (Builder $query): Builder => $query->where('status', SubmissionStatus::Published)
                    ),
            ];
        }
        return array_merge($tabs, [
            'Declined' => Tab::make("Declined")
                ->modifyQueryUsing(
                    fn (Builder $query): Builder => $query->where('status', SubmissionStatus::Declined)
                ),
        ]);
    }
}
