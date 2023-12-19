<div class="space-y-6">
    <div class="flex items-center">
        <div class="flex space-x-3 justify-center items-center">
            <h3 class="text-xl font-semibold leading-6 text-gray-950 dark:text-white">
                Editing
            </h3>
            @if($this->isStageOpen())
                <x-filament::badge color="success">Open</x-filament::badge>
            @else
                <x-filament::badge color="warning">Close</x-filament::badge>
            @endif
        </div>
        @livewire(App\Panel\Livewire\Workflows\Components\StageSchedule::class, ['stage' => $this->getStage()])
    </div>
    <div>
        <form wire:submit='save' class="space-y-4">
            {{ $this->form }}
            <x-filament::button type="submit" color="primary" icon='lineawesome-save-solid'>
                {{ __('Save') }}
            </x-filament::button>
        </form>
    </div>
</div>
