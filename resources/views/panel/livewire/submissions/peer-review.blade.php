<div class="space-y-6">
  @if($stageOpened && $submission->stage == App\Models\Enums\SubmissionStage::PeerReview)
    <div class="grid grid-cols-12 gap-4">
      <div class="space-y-4 col-span-8">
          @livewire(App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable::class, ['record' => $submission, 'category' => \App\Constants\SubmissionFileCategory::PAPERS])
          @livewire(App\Panel\Livewire\Submissions\Components\ReviewerList::class, ['record' => $submission])
          @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\Discussions::class, ['record' => $submission])
      </div>
      <div class="self-start sticky top-24 flex flex-col gap-3 col-span-4">
        {{-- TODO: is this a good way --}}
        @hasanyrole([\App\Models\Enums\UserRole::Admin->value, \App\Models\Enums\UserRole::Editor->value])
          @if($submission->stage == App\Models\Enums\SubmissionStage::PeerReview)
            {{ $this->skipReviewAction() }}
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