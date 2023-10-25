<x-filament-widgets::widget>
    <x-filament::section icon='heroicon-m-megaphone' icon-color='info'>
        <x-slot name='heading'>Announcement</x-slot>
        
        @if (is_null($announcement))
            <h2 class="text-xl text-center text-gray-900 dark:text-white">Now there's no announcement here</h2>
        @else 
            <x-header.conference>
                <x-slot:conference_date>
                    {{ $announcement->created_at }}
                </x-slot:conference_date>
                
                <x-slot:conference_title>
                    {{ $announcement->title }}
                </x-slot:conference_title>
                
                <x-slot:conference_description>
                    {!! $announcement->getMeta('content') !!}
                </x-slot:conference_description>

                <x-slot:conference_type>
                
                </x-slot:conference_type>
                
                <x-slot:conference_browsur>
                    <img src="{{ $announcement->getFirstMedia('featured_image') ? $announcement->getFirstMedia('featured_image')->getAvailableUrl(['small', 'thumb', 'thumb-xl']) : '' }}" alt="">
                </x-slot:conference_browsur>
                
            </x-header.conference>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
