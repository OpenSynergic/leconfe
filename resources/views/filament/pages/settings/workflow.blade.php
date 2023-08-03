<x-filament::page>
    {{-- <x-tabs>
        <x-slot:buttons>
            <x-tabs.button>Submission</x-tabs.button>
            <x-tabs.button>Review</x-tabs.button>
        </x-slot:buttons>

        <x-tabs.content>
            <x-vertical-tabs>
                <x-slot:buttons>
                    <x-vertical-tabs.button>Disable Submission</x-vertical-tabs.button>
                    <x-vertical-tabs.button>Review</x-vertical-tabs.button>
                </x-slot:buttons>

                <x-vertical-tabs.content>
                  <livewire:forms.disable-submission-form />
                </x-vertical-tabs.content>
                <x-vertical-tabs.content>
                    Review Content
                </x-vertical-tabs.content>
            </x-vertical-tabs>
        </x-tabs.content>
        <x-tabs.content>
            Review Content
        </x-tabs.content>
    </x-tabs> --}}

    <form wire:submit="submit" class="space-y-4">
        {{ $this->form }}

        <x-filament::button type="submit">
            Submit
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</x-filament::page>
