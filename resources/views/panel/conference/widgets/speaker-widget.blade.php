<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-m-user-group" icon-color="info">
        <x-slot name="heading">Speaker</x-slot>
    
        @if ($participants->isEmpty())
            <h2 class="text-xl text-center text-gray-900 dark:text-white">Now there's no speaker here</h2>
        @else
            <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2">
                @foreach ($participants as $participant)
                    <div class="image mx-auto py-2">
                        <img src="{{ $participant->getFirstMedia('profile') ? $participant->getFirstMedia('profile')->getFullUrl('avatar') : 'https://ui-avatars.com/api/?name=' . urlencode($participant->fullname) . '&color=FFFFFF&background=09090b' }}" alt="" class="w-11 h-11 rounded-full mx-auto">
                        <div class="text-center">
                            {{-- Speaker name --}}
                            <h2 class="text-sm text-gray-900 dark:text-white">{{ $participant->fullName }}</h2>
                    
                            {{-- Expertise from the speaker --}}
                            <p class="text-xs text-gray-500">
                                @if ($participant->getMeta('expertise'))
                                    @foreach ($participant->getMeta('expertise') as $expertise)
                                        @unless ($loop->first)
                                            ,
                                        @endunless
                                        {{ $expertise }}
                                    @endforeach
                                @endif                        
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
    </x-filament::section>
</x-filament-widgets::widget>
