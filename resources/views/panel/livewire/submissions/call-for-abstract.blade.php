@php
    use App\Panel\Livewire\Submissions\Components;
    use App\Models\Enums\SubmissionStage;
    use App\Constants\SubmissionFileCategory;
@endphp
<div class="space-y-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="space-y-4 col-span-8">
            @livewire(Components\Files\AbstractFiles::class, ['submission' => $submission, 'category' => SubmissionFileCategory::SUPPLEMENTARY_FILES, 'lazy' => true])

            {{-- @livewire(SubmissionDetail\Discussions::class, ['record' => $submission, 'lazy' => true]) --}}
        </div>
        <div class="self-start sticky top-24 flex flex-col gap-3 col-span-4">
            @if ($submission->stage == SubmissionStage::PeerReview && !$reviewStageOpen)
                <div class="bg-primary-700 p-4 rounded-lg text-base text-white">
                    This submission has been accepted. Now, we are waiting to next stage is open.
                </div>
            @endif
            @if($submission->status == \App\Models\Enums\SubmissionStatus::Queued)
                <div class="space-y-4">
                    @can('acceptAbstract', $submission)
                        {{ $this->acceptAction() }}
                    @endcan
                    @can('declineAbstract', $submission)
                        {{ $this->declineAction() }}
                    @endcan
                </div>
                @endif
                {{-- Participants --}}
                @livewire(Components\ParticipantList::class, ['submission' => $submission, 'lazy' => true])
            {{-- @endhasanyrole --}}
        </div>
    </div>
    <x-filament-actions::modals />
</div>
