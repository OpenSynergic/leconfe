<x-filament::page :class="\Illuminate\Support\Arr::toCssClasses([
    'filament-resources-list-records-page',
    'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
])">
    <div>
        <x-tabs>
            <x-slot:buttons>
                <x-tabs.button>New</x-tabs.button>
                <x-tabs.button>Review</x-tabs.button>
                <x-tabs.button>Archived</x-tabs.button>
            </x-slot:buttons>
        </x-tabs.button>
    
        {{ $this->table }}
    </div>
</x-filament::page>
