<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-m-clock" icon-color="info">
        <x-slot name="heading">Timeline</x-slot>
        @if ($timeline->isEmpty())
            <h2 class="text-xl text-center text-gray-900 dark:text-white">Now there's no timeline here</h2>
        @else 
            @foreach ($timeline as $timelines)
                <x-content.timeline>
                    <x-slot:timeline_time>
                        {{ $timelines->date->format('d-m-Y') }}
                    </x-slot:timeline_time>
                    
                    <x-slot:timeline_title>
                        {{ $timelines->title }}
                    </x-slot:timeline_title>
                    
                    <x-slot:timeline_description>
                        {{ $timelines->subtitle }}
                    </x-slot:timeline_description>
                </x-content.timeline>
            @endforeach
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
