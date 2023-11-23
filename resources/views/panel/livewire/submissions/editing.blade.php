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
            @livewire(App\Panel\Livewire\Submissions\Components\ParticipantList::class, ['submission' => $submission, 'lazy' => true])
            
            @if(auth()->user()->can('Submission:publish') && $submission->stage == App\Models\Enums\SubmissionStage::Editing)
            <x-filament::button x-on:click="$dispatch('open-publication-tab')" class="w-full">
                    Publish
                </x-filament::button>
            @endif
        </div>
    </div>
</div>