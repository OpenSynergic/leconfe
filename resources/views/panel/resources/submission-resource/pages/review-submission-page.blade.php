<x-filament-panels::page>
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-8 space-y-4">
            @livewire(App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable::class, ['record' => $record, 'category' => 'reviewer-assigned-papers', 'viewOnly' => true])
            {{ $this->reviewForm }}
            @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\Discussions::class, ['record' => $record, 'lazy' => true])
        </div>
        <div class="col-span-4 space-y-4 self-start sticky top-20">
            {{ $this->infolist }}
            {{ $this->recommendationForm }}
            <x-filament::button type="submit" wire:click='submit' class="w-full mt-4" icon="lineawesome-check-circle-solid" :outlined="true"> 
                Submit Review
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
