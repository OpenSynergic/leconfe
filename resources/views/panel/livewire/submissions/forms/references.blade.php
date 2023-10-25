<x-filament::section heading="References">
    <form wire:submit='submit' class="space-y-4">
        {{ $this->form }}
        @if(auth()->user()->can('Publication:update') && !$submission->isPublished())
            <x-filament::button type="submit" icon="iconpark-save-o">
                Submit
            </x-filament::button>
        @endif
    </form>
</x-filament::section>