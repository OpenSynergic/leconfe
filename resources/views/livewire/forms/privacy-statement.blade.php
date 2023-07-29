<div class="space-y-6">
    <x-filament::form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </x-filament::form>
</div>
