<div class="space-y-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="space-y-4 col-span-8">
            {{-- Draft Files --}}
            @livewire(App\Panel\Livewire\Submissions\Components\Files\DraftFiles::class, ['submission' => $submission, 'lazy' => true])

            {{-- Edited Files --}}
            @livewire(App\Panel\Livewire\Submissions\Components\Files\EditedFiles::class, ['submission' => $submission, 'lazy' => true])
        </div>
        <div class="space-y-4 col-span-4">

            {{-- Participants --}}
            @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\AssignParticipants::class, ['submission' => $submission, 'lazy' => true])

            {{ $this->publishAction() }}
        </div>
    </div>
</div>