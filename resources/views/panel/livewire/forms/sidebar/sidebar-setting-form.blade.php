<div>
    <div class="space-y-2">
        <form wire:submit='submit'>
            {{ $this->form }}
            <x-filament::button wire:click="submit" class="mt-3" type="button">
                {{ 'Save' }}
            </x-filament::button>
        </form>
    </div>
</div>
