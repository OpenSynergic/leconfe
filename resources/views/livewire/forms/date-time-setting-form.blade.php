<div class="space-y-6">
    {{-- <div>
        <h2 class="text-base font-semibold">Date and Time Formats</h2>
        <p class="text-sm">Please select the desired format for dates and times. You may also enter a custom format using
            special <a href="https://www.php.net/manual/en/function.strftime.php#refsect1-function.strftime-parameters" target="_blank"
                class="filament-link inline-flex items-center justify-center gap-0.5 font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 filament-tables-link-action">format
                characters</a>.
        </p>
    </div> --}}
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">Save</x-filament::button>
    </form>
</div>
