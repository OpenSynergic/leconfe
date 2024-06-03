@use('App\Panel\Conference\Livewire\Submissions\Components')
@use('App\Models\Enums\SubmissionStage')
@use('App\Constants\SubmissionFileCategory')
<div class="space-y-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-8 space-y-4">
            @livewire(Components\Files\AbstractFiles::class, ['submission' => $submission, 'category' => SubmissionFileCategory::SUPPLEMENTARY_FILES])

            @livewire(Components\Discussions\DiscussionTopic::class, ['submission' => $submission, 'stage' => SubmissionStage::CallforAbstract, 'lazy' => true])
        </div>
        <div class="sticky z-30 flex flex-col self-start col-span-4 gap-3 top-24">
            @if ($submission->stage == SubmissionStage::PeerReview && !$reviewStageOpen)
                <div class="p-4 text-base text-white rounded-lg bg-primary-700">
                    This submission has been accepted. Now, we are waiting to next stage is open.
                </div>
            @endif
            @if($submission->status == \App\Models\Enums\SubmissionStatus::Queued)
                <div class="space-y-4">
                    @if($submission->getEditors()->isEmpty() && ! auth()->user()->hasRole(\App\Models\Enums\UserRole::Editor->value))
                        <div class="px-4 py-3.5 text-base text-white rounded-lg border-2 border-primary-700 bg-primary-500">
                            Assign an editor to enable the editorial decisions for this stage.
                        </div>
                    @else
                        @can('acceptAbstract', $submission)
                            {{ $this->acceptAction() }}
                        @endcan
                        @can('declineAbstract', $submission)
                            {{ $this->declineAction() }}
                        @endcan
                    @endif
                </div>
            @endif
            @livewire(Components\ParticipantList::class, ['submission' => $submission])
        </div>
    </div>
    <x-filament-actions::modals />
</div>
