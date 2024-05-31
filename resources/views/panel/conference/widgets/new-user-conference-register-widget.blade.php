<x-filament-widgets::widget>
    <x-filament::section class="!bg-primary-300/30">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <x-iconpark-pin class="w-8 h-8 text-primary-800" />
                <div class="flex flex-col ml-3">
                    <div class="font-medium leading-none">Register Now for the Conference</div>
                    <p class="text-sm text-gray-600 leading-none mt-1">
                        We are excited to invite you to our conference. Click register and be a part of this event.
                    </p>
                </div>
            </div>
            <x-filament::button tag="a" target="_blank" href="{{ route('filament.conference.pages.profile', ['tab' => '-roles-tab']) }}">
                Regiter Now
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>