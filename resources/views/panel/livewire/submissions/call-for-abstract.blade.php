<div class="space-y-6">
  <div class="flex gap-4">
    <div class="space-y-4">
      @livewire(App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable::class, ['record' => $submission, 'category' => 'submission-files'])
      @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\Discussions::class, ['submission' => $submission])
    </div>
      <div class="w-3/6 self-start sticky top-24 flex flex-col gap-3">
        @if($submission->accepted())
          <div class="bg-primary-700 p-4 rounded-lg text-base">
            Your submission has been accepted. Now, we are waiting to next stage is open.
          </div>
        @endif
        <div id="callforabstract-action" class="space-y-4">
          @if(! $submission->accepted())
            {{ $this->acceptAction() }}
            {{ $this->declineAction() }}
          @endif
        </div>
        <div id="participants">
          @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\AssignParticipants::class, ['submission' => $submission])
        </div>
    </div>
  </div>
  <x-filament-actions::modals />
</div>