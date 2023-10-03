<div class="space-y-6">
  <div class="flex gap-4">
    <div class="w-full">
      {{ $this->infolist }}
    </div>
      <div class="max-w-xs self-start sticky top-24 flex flex-col gap-3">
        <div class="bg-primary-700 p-4 rounded-lg text-base">
          Your submission has been accepted. Now, we are waiting to next stage is open.
        </div>
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