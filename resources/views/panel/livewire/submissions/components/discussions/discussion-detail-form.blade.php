<div>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}
        @if($topic->open)
            <div class="flex justify-end">
                <x-filament::button type="submit" outlined="true" form="form">
                    {{ __('Add Message') }}
                </x-filament::button>
            </div>
        @endif
    </form>
</div>