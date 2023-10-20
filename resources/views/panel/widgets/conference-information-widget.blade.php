<x-filament-widgets::widget>
    <x-filament::section icon='heroicon-m-megaphone' icon-color='info'>
        <x-slot name='heading'>Announcement</x-slot>
        <x-header.conference>
            <x-slot:conference_date>
                {{ $announcement ? $announcement->created_at : '  ' }}
            </x-slot:conference_date>

            <x-slot:conference_title>
                {{ $announcement ? $announcement->title : '  ' }}   
            </x-slot:conference_title>

            <x-slot:conference_description>
               {!! $announcement ? $announcement->getMeta('content') : '' !!}
            </x-slot:conference_description>

            <x-slot:conference_type>

            </x-slot:conference_type>

            <x-slot:conference_browsur>
                {{-- <img src="{{ $announcement ->getFirstMedia('featured_image') ? $announcement ->getFirstMedia('featured_image')->getAvailableUrl(['small', 'thumb', 'thumb-xl']) : '' }}" alt="" width="300px" class="rounded"> --}}
                @if ($announcement)
                    <img src="{{ $announcement->getFirstMedia('featured_image')->getAvailableUrl(['small', 'thumb', 'thumb-xl']) }}" alt="" width="300px" class="rounded">
                @else
                    <p>  </p>
                @endif

            </x-slot:conference_browsur>


        </x-header.conference>

    </x-filament::section>
</x-filament-widgets::widget>
