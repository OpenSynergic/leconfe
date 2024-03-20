<div class="space-y-6">
    <div class="flex items-center">
        <div class="flex space-x-3 justify-center items-center">
            <h3 class="text-xl font-semibold leading-6 text-gray-950 dark:text-white">
                Peer Review
            </h3>
            @if($this->isStageOpen())
                <x-filament::badge color="success">Open</x-filament::badge>
            @else
                <x-filament::badge color="warning">Close</x-filament::badge>
            @endif
        </div>
        @livewire(App\Panel\Conference\Livewire\Workflows\Components\StageSchedule::class, ['stage' => $this->getStage()])
    </div>
    <div class="space-y-4">
        {{ $this->form }}
        {{ $this->submitAction() }}
    </div>
</div>
