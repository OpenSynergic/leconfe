<div class="space-y-6">
    @if ($stageOpened && $submission->stage != App\Models\Enums\SubmissionStage::CallforAbstract)
        <div class="grid grid-cols-12 gap-4">
            <div class="space-y-4 col-span-8">
                {{-- Papers --}}
                @livewire(App\Panel\Livewire\Submissions\Components\Files\PaperFiles::class, ['submission' => $submission])

                {{-- Reviewer List --}}
                @livewire(App\Panel\Livewire\Submissions\Components\ReviewerList::class, ['record' => $submission])

                {{-- Revision Files --}}
                @livewire(App\Panel\Livewire\Submissions\Components\Files\RevisionFiles::class, ['submission' => $submission])

                {{-- Discussions --}}
                @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\Discussions::class, ['record' => $submission])
            </div>
            <div class="self-start sticky top-24 flex flex-col gap-4 col-span-4">
                @if ($submission->revision_required)
                    <div class="flex space-x-2 items-center p-4 mb-4 text-sm text-slate-800 rounded-lg bg-white border border-warning-200 dark:border-warning-800 dark:bg-warning-600 dark:text-white"
                        role="alert">
                        {{-- <x-lineawesome-check-circle-solid class="w-5 h-5 text-success-600"/> --}}
                        <span class="text-base text-center">
                            Revisions have been requested.
                        </span>
                    </div>
                @endif

                {{-- Participants --}}
                @livewire(App\Panel\Livewire\Submissions\Components\ParticipantList::class, ['submission' => $submission, 'lazy' => true])

                {{-- TODO: is this a good way using hasanyrole --}}
         
                    @hasanyrole([\App\Models\Enums\UserRole::Admin->value, \App\Models\Enums\UserRole::Editor->value])
                        @if (!$submission->reviews()->exists() && $submission->stage == \App\Models\Enums\SubmissionStage::PeerReview)
                            {{ $this->skipReviewAction() }}
                        @endif
                        @if (
                            $submission->stage != App\Models\Enums\SubmissionStage::Editing &&
                                $submission->status != App\Models\Enums\SubmissionStatus::Declined)
                            {{ $this->requestRevisionAction() }}
                            {{ $this->acceptSubmissionAction() }}
                            {{ $this->declineSubmissionAction() }}
                        @endif
                    @endhasanyrole
            </div>
        </div>
        <x-filament-actions::modals />
    @elseif($submission->stage == App\Models\Enums\SubmissionStage::CallforAbstract)
        <div class="bg-warning-700 p-4 rounded-lg text-base">
            Can not enter the stage until the submission is accepted.
        </div>
    @else
        <div class="bg-warning-700 p-4 rounded-lg text-base">
            The stage has not yet been opened.
        </div>
    @endif
</div>
