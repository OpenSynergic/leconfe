<x-filament::page>
    <x-tabs>
        <x-slot:buttons>
            <x-tabs.button>Appearance</x-tabs.button>
            <x-tabs.button>Setup</x-tabs.button>
        </x-slot:buttons>

        <x-tabs.content>
            Appearance
        </x-tabs.content>
        <x-tabs.content>
          <x-vertical-tabs>
            <x-slot:buttons>
                <x-vertical-tabs.button>Date & Time</x-vertical-tabs.button>
                <x-vertical-tabs.button>Privacy Statement</x-vertical-tabs.button>
            </x-slot:buttons>

            <x-vertical-tabs.content>
              {{-- <livewire:forms.date-time-setting-form /> --}}
            </x-vertical-tabs.content>
            <x-vertical-tabs.content>
              {{-- <livewire:forms.privacy-statement /> --}}
            </x-vertical-tabs.content>
        </x-vertical-tabs>
        </x-tabs.content>
    </x-tabs>
</x-filament::page>
