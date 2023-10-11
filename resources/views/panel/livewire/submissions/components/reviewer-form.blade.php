<div>
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-8 space-y-6">
            @livewire(App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable::class, ['record' => $submission, 'category' => 'reviewer-assigned-papers', 'viewOnly' => true])
            {{ $this->formReview }}
            @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\Discussions::class, ['record' => $submission, 'lazy' => true])
        </div>
        <div class="self-start sticky top-24 col-span-4 space-y-6">
            <form wire:submit='submit'>
                {{ $this->formDecision }}
                <x-filament::button type="submit" class="w-full mt-4"> 
                    Submit
                </x-filament::button>
            </form>
        </div>
    </div>
</div>