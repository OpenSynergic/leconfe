<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-m-clock" icon-color="info">
        <x-slot name="heading">Timeline</x-slot>
                <x-content.timeline>
                    <x-slot:timeline_time>
                        12 Nov 2023
                    </x-slot:timeline_time>

                    <x-slot:timeline_title>
                        Timeline One
                    </x-slot:timeline_title>

                    <x-slot:timeline_description>
                     Timeline one description
                    </x-slot:timeline_description>

                </x-content.timeline>

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

                </x-content.timeline>
    </x-filament::section>
</x-filament-widgets::widget>
