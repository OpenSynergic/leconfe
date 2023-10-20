<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-m-user-group" icon-color="info">
        <x-slot name="heading">Speaker</x-slot>
    

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
            @foreach ($participants as $participant)
                <div class="image">
                    <img src="{{ $participant->getFirstMedia('profile') ? $participant->getFirstMedia('profile')->getAvailableUrl(['avatar', 'thumb', 'thumb-xl']) : '' }}" alt="" class="w-11 h-11 rounded-full mx-auto">
                <div class="text-center">
                    <h2 class="text-sm text-gray-900 dark:text-white">{{ $participant->given_name }} {{ $participant->family_name }}</h2>
                    <p class="text-xs text-gray-500">
                        @if ($participant->getMeta('expertise'))
                            @foreach ($participant->getMeta('expertise') as $expertise)
                                {{ $expertise }},
                            @endforeach
                        @endif
                    </p>
                </div>
            </div>
            @endforeach
        </div>



    </x-filament::section>
</x-filament-widgets::widget>
    