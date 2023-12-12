<div>
    <x-filament::section heading="Submission Detail">
        <form wire:submit='submit'>
            <div class="space-y-4">
                {{ $this->form }}
                @can('editing', $submission)
                    <x-filament::button type="submit" icon="iconpark-save-o">
                        Save
                    </x-filament::button>
                @endcan
            </div>
        </form>
    </x-filament::section>
</div>
