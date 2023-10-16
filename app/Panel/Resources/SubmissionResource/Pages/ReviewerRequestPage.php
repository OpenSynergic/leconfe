<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Constants\ReviewerStatus;
use App\Constants\SubmissionFileCategory;
use App\Infolists\Components\LivewireEntry;
use App\Models\Submission;
use App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable;
use App\Panel\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ReviewerRequestPage extends Page implements HasInfolists, HasActions
{
    use InteractsWithInfolists, InteractsWithActions;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.reviewer-request-page';

    public Submission $record;

    public function getHeading(): string|Htmlable
    {
        return 'Reviewer Request: ' . $this->record->getMeta('title');
    }

    public function acceptAction()
    {
        return Action::make('acceptAction')
            ->label("Accept Request")
            ->icon("lineawesome-check-circle-solid")
            ->color('primary')
            ->outlined()
            ->requiresConfirmation()
            ->successNotificationTitle("Request Accepted")
            ->action(function (Action $action) {
                $this->record
                    ->reviews()
                    ->where('participant_id', auth()->user()->asParticipant()->getKey())->update([
                        'date_confirmed' => now(),
                        'status' => ReviewerStatus::ACCEPTED
                    ]);
                $action->success();
                $action->redirect(SubmissionResource::getUrl('review', ['record' => $this->record->id]));
            });
    }

    public function declineAction()
    {
        return Action::make('declineAction')
            ->label("Decline Request")
            ->icon("lineawesome-times-circle-solid")
            ->outlined()
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
            });
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make()
                    ->aside()
                    ->heading("Request for review")
                    ->description("You have been selected as a potential reviewer of the following submission. Below is an overview of the submission, as well as the timeline for this review. We hope that you are able to participate")
                    ->schema([
                        Fieldset::make("Submission Details")
                            ->schema([
                                TextEntry::make('Title')
                                    ->getStateUsing(fn (Submission $submission) => $submission->getMeta('title')),
                                TextEntry::make('Keyword')
                                    ->getStateUsing(function (Submission $submission) {
                                        return $submission->tagsWithType('submissionKeywords')->pluck('name')->join(', ');
                                    }),
                                TextEntry::make('Abstract')
                                    ->html()
                                    ->columnSpanFull()
                                    ->getStateUsing(fn (Submission $submission) => $submission->getMeta('abstract'))
                            ]),
                        LivewireEntry::make("review-files")
                            ->livewire(SubmissionFilesTable::class, [
                                'record' => $this->record,
                                'category' => SubmissionFileCategory::PAPERS,
                                'viewOnly' => true
                            ]),
                        // Fieldset::make("Review Schedule")
                        //     ->columns(3)
                        //     ->schema([
                        //         TextEntry::make('Editor Due Date')
                        //             ->getStateUsing("20 March 2023"),
                        //         TextEntry::make('Response Due Date')
                        //             ->getStateUsing("20 March 2023"),
                        //         TextEntry::make('Review Due Date')
                        //             ->getStateUsing("20 March 2023"),
                        //     ])
                    ])
            ]);
    }
}
