<div>
    <x-filament::section>
    {{ $this->infolist }}
        
        <form wire:submit='submit'>
            <div class="mt-5 space-y-4">
                {{ $this->form }}
                @can('editing', $submission)
                    <x-filament::button type="submit" icon="iconpark-save-o">
                        Submit
                    </x-filament::button>
                @endcan
            </div>
        </form>

        <x-filament-actions::modals />
    </x-filament::section>
</div>