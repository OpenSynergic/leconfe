<x-filament-widgets::widget>
    <x-filament::section icon='heroicon-m-megaphone' icon-color='info'>
        <x-slot name='heading'>Latest Announcement</x-slot>

        @if (is_null($announcement))
            <h2 class="text-xl text-center text-gray-900 dark:text-white">Now there's no announcement here</h2>
        @else
            <div class="flex flex-col sm:flex-row justify-between gap-2 flex-wrap">
                <div class="flex-1">
                    <p class="text-sm text-gray-500">{{ $announcement->created_at?->format(setting('format.date')) }}</p>
                    <div class="prose max-w-none">
                        <h2 class="">
                            {{ $announcement->title }}</h2>
                        <p class="">{!! $announcement->getMeta('content') !!}</p>
                    </div>
                </div>
                @if($announcement->getFirstMedia('featured_image'))
                    <div class="flex-shrink w-max">
                        <img class="flex-shrink" src="{{ $announcement->getFirstMedia('featured_image')->getAvailableUrl(['small', 'thumb', 'thumb-xl']) }}"
                            alt="announcement-cover">
                    </div>
                @endif
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
