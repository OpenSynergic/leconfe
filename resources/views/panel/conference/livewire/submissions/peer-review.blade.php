@use('App\Models\Enums\SubmissionStage')
@use('App\Models\Enums\SubmissionStatus')
@use('App\Panel\Conference\Livewire\Submissions\Components')
@use('App\Models\Enums\UserRole')

@php
    $user = auth()->user();
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="space-y-4 col-span-8">
            {{-- Papers --}}
            @livewire(Components\Files\PaperFiles::class, ['submission' => $submission])

            {{-- Reviewer List --}}
            @livewire(Components\ReviewerList::class, ['record' => $submission])

            {{-- Revision Files --}}
            @livewire(Components\Files\RevisionFiles::class, ['submission' => $submission])

            {{-- Discussions --}}
            @livewire(Components\Discussions\DiscussionTopic::class, ['submission' => $submission, 'stage' => SubmissionStage::PeerReview, 'lazy' => true])
        </div>
        
        <div class="self-start sticky z-30 top-24 flex flex-col gap-4 col-span-4" x-data="{ decision:@js($submissionDecision) }">
            @if($submission->stage != SubmissionStage::CallforAbstract)    
                @if ($submission->revision_required)
                    <div class="flex items-center p-4 text-sm border rounded-lg border-warning-400 bg-warning-200 text-warning-600" x-show="!decision" role="alert">
                        <span class="text-base text-center">
                            Revisions have been requested.
                        </span>
                    </div>
                @endif

                @if($submission->getEditors()->isEmpty())
                    <div class="px-4 py-3.5 text-base text-white rounded-lg border-2 border-primary-700 bg-primary-500">
                        Assign an editor to enable the editorial decisions for this stage.
                    </div>
                @else
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 space-y-3 py-5 px-6" x-show="decision">
                        <div class="text-base">
                            @if ($submission->status == SubmissionStatus::Declined)
                                Submission Declined
                            @elseif ($submission->skipped_review)
                                Submission skipped for review.
                            @else
                                Submission accepted for review.
                            @endif
                        </div>
                        <a href="#" @@click="decision = !decision" class="text-sm text-primary-500 underline">
                            Change Decision
                        </a>
                    </div>
                    <div @class([
                        'flex flex-col gap-4 col-span-4',
                        'hidden' => in_array($submission->status, [SubmissionStatus::Queued, SubmissionStatus::Published]),
                    ]) x-show="!decision">
                        @if ($user->can('skipReview', $submission) && ! $submission->skipped_review)
                            {{ $this->skipReviewAction() }}
                        @endif
                        @if ($user->can('requestRevision', $submission) && ! $submission->revision_required)
                            {{ $this->requestRevisionAction() }}
                        @endcan
                        @if ($user->can('acceptPaper', $submission) && ($submission->status != SubmissionStatus::Editing || $submission->skipped_review))
                            {{ $this->acceptSubmissionAction() }}
                        @endcan
                        @if ($user->can('declinePaper', $submission) && ! in_array($submission->status, [SubmissionStatus::Declined]))
                            {{ $this->declineSubmissionAction() }}
                        @endcan
                    </div>
                @endif
            @endif
            
            @livewire(Components\ParticipantList::class, ['submission' => $submission, 'lazy' => true])
        </div>

    </div>
    <x-filament-actions::modals />
</div>
