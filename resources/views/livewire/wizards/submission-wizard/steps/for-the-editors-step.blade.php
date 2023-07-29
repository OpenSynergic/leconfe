<x-filament::form wire:submit.prevent="submit">
    {{ $this->form }}
    <div class="flex items-center justify-between">
        <div>
            <x-forms::button icon="heroicon-o-chevron-left" x-show="! isFirstStep()" x-cloak x-on:click="previousStep"
                color="secondary" size="sm">
                {{ __('forms::components.wizard.buttons.previous_step.label') }}
            </x-forms::button>
        </div>

        <div>
            <x-forms::button type="submit" icon="heroicon-o-chevron-right" icon-position="after" x-show="! isLastStep()" x-cloak
                wire:loading.class.delay="opacity-70 cursor-wait" size="sm">
                {{ __('forms::components.wizard.buttons.next_step.label') }}
            </x-forms::button>
        </div>
    </div>
</x-filament::form>
