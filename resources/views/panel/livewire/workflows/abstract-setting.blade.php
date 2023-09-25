<div class="space-y-6">
    <div class="flex items-center">
        <div>
            <h3 class="text-xl font-semibold leading-6 text-gray-950 dark:text-white">
                Call for Abstract
            </h3>
        </div>
        @livewire(App\Panel\Livewire\Workflows\Components\StageSchedule::class, ['stage' => $this->getStage()])
    </div>
    <div>
        {{ $this->form }}
    </div>
</div>
