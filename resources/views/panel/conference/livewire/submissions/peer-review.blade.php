@use('App\Models\Enums\SubmissionStage')
@use('App\Models\Enums\SubmissionStatus')
@use('App\Panel\Conference\Livewire\Submissions\Components')
@use('App\Models\Enums\UserRole')
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
        <div class="self-start sticky z-30 top-24 flex flex-col gap-4 col-span-4">
            @if ($submission->revision_required)
                <div class="flex items-center p-4 text-sm border rounded-lg border-warning-400 bg-warning-200 text-warning-600"
                    role="alert">
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
                @if ($submission->stage == SubmissionStage::PeerReview && $submission->status == SubmissionStatus::OnReview)
                    @can('skipReview', $submission)
                        {{ $this->skipReviewAction() }}
                    @endcan
                    @can('requestRevision', $submission)
                        {{ $this->requestRevisionAction() }}
                    @endcan
                    @can('acceptPaper', $submission)
                        {{ $this->acceptSubmissionAction() }}
                    @endcan
                    @can('declinePaper', $submission)
                        {{ $this->declineSubmissionAction() }}
                    @endcan
                @endif
            @endif

            @livewire(Components\ParticipantList::class, ['submission' => $submission, 'lazy' => true])
        </div>

    </div>
    <x-filament-actions::modals />
</div>
