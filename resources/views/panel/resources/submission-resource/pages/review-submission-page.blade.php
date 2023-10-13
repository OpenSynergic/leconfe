<x-filament-panels::page>
    <div class="ml-auto flex">
        <x-filament::button x-on:click="$dispatch('open-modal', {'id': 'guidelines'})" color="info" icon="iconpark-info-o">
            View Guidelines
        </x-filament::button>
    </div>
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
    <x-filament::modal id="guidelines" :slide-over="true" width="xl">
        <x-slot name="heading">
            <h1 class="text-xl font-bold">
                Review Guidlines & Competing Interests
            </h1>
        </x-slot>
        <x-slot name="description">
            Please read the following guidelines and competing interests before submitting your review.
        </x-slot>
        <div class="flex flex-col space-y-4">
            <div>
                <h2 class="text-lg font-bold">
                    Review Guidelienes
                </h2>
                {!! $conference->getMeta('review_guidelines') !!}
            </div>
            <div>
                <h2 class="text-lg font-bold">
                    Competing Interests
                </h2>
                {!! $conference->getMeta('competing_interests') !!}
            </div>
            <x-slot name="footerActions">
                <x-filament::button color="gray" x-on:click="$dispatch('close-modal', {'id': 'guidelines'})">
                    Close
                </x-filament::button>
            </x-slot>
        </div>
    </x-filament::modal>
</x-filament-panels::page>
