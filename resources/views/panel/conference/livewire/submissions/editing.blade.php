<div class="space-y-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="space-y-4 col-span-8">
            {{-- Draft Files --}}
            @livewire(App\Panel\Conference\Livewire\Submissions\Components\Files\DraftFiles::class, ['submission' => $submission])

            {{-- Edited Files --}}
            @livewire(App\Panel\Conference\Livewire\Submissions\Components\Files\ProductionFiles::class, ['submission' => $submission])
        </div>
        <div class="space-y-4 col-span-4">

            {{-- Participants --}}
            @livewire(App\Panel\Conference\Livewire\Submissions\Components\ParticipantList::class, ['submission' => $submission])
            
        </div>
    </div>
</div>
