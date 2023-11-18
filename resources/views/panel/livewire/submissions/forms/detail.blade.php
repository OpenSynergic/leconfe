<div>
    <x-filament::section heading="Submission Detail">
        <form wire:submit='submit'>
            <div class="space-y-4">
                {{ $this->form }}
                @if(!$submission->isPublished() && auth()->user()->can('Publication:update'))
                    <x-filament::button type="submit" icon="iconpark-save-o">
                        Save
                    </x-filament::button>
                @endif
            </div>
        </form>
    </x-filament::section>
</div>
