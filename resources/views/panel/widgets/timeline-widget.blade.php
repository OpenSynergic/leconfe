<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-m-clock" icon-color="info">
        <x-slot name="heading">Timeline</x-slot>
                @foreach ( $timeline as $timelines )
                    
                <x-content.timeline>
                    <x-slot:timeline_time>
                       {{ $timelines->date->format('d-m-Y') }}
                    </x-slot:timeline_time>

                    <x-slot:timeline_title>
                        {{ $timelines->title  }}
                    </x-slot:timeline_title>

                    <x-slot:timeline_description>
                    {{ $timelines->subtitle }}
                    </x-slot:timeline_description>

                </x-content.timeline>
                @endforeach
{{-- 
                <x-content.timeline>
                    <x-slot:timeline_time>
                        15 Nov 2024
                    </x-slot:timeline_time>

                    <x-slot:timeline_title>
                        Timeline Two
                    </x-slot:timeline_title>

                    <x-slot:timeline_description>
                     Timeline two description
                    </x-slot:timeline_description>

                </x-content.timeline> --}}
    </x-filament::section>
</x-filament-widgets::widget>
