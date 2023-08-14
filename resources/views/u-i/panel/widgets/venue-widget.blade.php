<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-m-building-office-2" icon-color="info">
        <x-slot name="heading">Venue</x-slot>
        <div class="flex gap-3">
            <div class="flex border">
               {{ $image ?? '' }}
            </div>

            <div class="flex flex-col">
                <div class="s">
                <x-filament::link size="sm" color='info' class="font-thin">
                   {{ $location ?? '' }}
                </x-filament::link>
                <p class="text-gray-500 text-xs">{{ $street ?? '' }}</p>
                </div>
        </div>

        </div>
        </x-content.venue>

    </x-filament::section>
</x-filament-widgets::widget>
