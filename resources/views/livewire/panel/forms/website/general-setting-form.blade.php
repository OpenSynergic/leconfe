<div>
    <form wire:submit="submit" class="space-y-4">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
