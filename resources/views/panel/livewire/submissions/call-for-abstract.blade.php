<div class="space-y-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="space-y-4 col-span-8">
            @livewire(App\Panel\Livewire\Submissions\Components\Files\SupplementaryFiles::class, ['submission' => $submission, 'category' => \App\Constants\SubmissionFileCategory::SUPPLEMENTARY_FILES, 'lazy' => true])

            @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\Discussions::class, ['record' => $submission, 'lazy' => true])
        </div>
        <div class="self-start sticky top-24 flex flex-col gap-3 col-span-4">
            @if ($submission->stage == App\Models\Enums\SubmissionStage::PeerReview && !$reviewStageOpen)
                <div class="bg-primary-700 p-4 rounded-lg text-base">
                    Your submission has been accepted. Now, we are waiting to next stage is open.
                </div>
            @endif
            @hasanyrole([\App\Models\Enums\UserRole::ConferenceManager->value, \App\Models\Enums\UserRole::Admin->value])
                @if($submission->stage == App\Models\Enums\SubmissionStage::CallforAbstract)
                    <div class="space-y-4">
                        {{ $this->acceptAction() }}
                        {{ $this->declineAction() }}
                    </div>
                @endif
                {{-- Participants --}}
                @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\AssignParticipants::class, ['submission' => $submission, 'lazy' => true])
            @endhasanyrole
        </div>
    </div>
    <x-filament-actions::modals />
</div>
