<div>
    <form wire:submit='submit'>
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-4" icon="iconpark-saveone-o">
            Submit
        </x-filament::button>
    </form>
</div>