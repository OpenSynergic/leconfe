<x-filament::page>
    <div class="mx-auto max-w-xl w-full space-y-6">
        <h1 class="font-bold text-2xl text-center">Make a Submission</h1>
        @if ($disable_submission)
            <div class="flex p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
                role="alert">
                <svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    This conference is not accepting submissions at this time.
                </div>
            </div>
        @else
            <div class="bg-white w-full rounded-xl p-4">
                <x-filament::form wire:submit.prevent="submit">
                    {{ $this->form }}

                    <x-filament::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
                </x-filament::form>
            </div>
        @endif
    </div>
</x-filament::page>
