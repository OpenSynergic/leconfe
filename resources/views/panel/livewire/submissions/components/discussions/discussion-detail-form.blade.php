<div>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}
        <div class="flex justify-end">
            <x-filament::button type="submit" outlined="true" form="form" :disabled="!$topic->open">
                {{ __('Add Message') }}
            </x-filament::button>
        </div>
    </form>
</div>