<x-filament-panels::page>
    <div class="flex flex-col gap-y-10">
        <div class="flex flex-col gap-y-6 bg-white p-6">
            @if (session()->has('success'))
                <div class="inline-flex items-center gap-1" x-data="{ show: true }"
                    x-effect="setTimeout(()=> {
                show = false;
                @this.$refresh();
                }, 1000)">
                    <x-heroicon-o-trash class="stroke-current shrink-0 h-6 w-6 text-gray-600" />
                    <span class="text-sm text-gray-600">{{ session('success') }}</span>
                </div>
            @endif
            <div class="flex flex-col gap-y-2">
                <h2 class="text-sm font-medium">Administrative Functions</h2>
                <div class="flex flex-col gap-y-2">
                    <div>
                        <x-filament::link class="text-blue-400 font-thin underline cursor-pointer">
                            System Information
                        </x-filament::link>
                    </div>

                    <div>
                        <x-filament::link class="text-blue-400 font-thin underline cursor-pointer" wire:click='expireUserSession'>
                            Expire User Session
                        </x-filament::link>
                    </div>

                    <div>
                        <x-filament::link class="text-blue-400 font-thin underline cursor-pointer"
                            wire:click='clearDataCache'>
                            Clear Data Caches
                        </x-filament::link>
                    </div>

                    <div>
                        <x-filament::link class="text-blue-400 font-thin underline cursor-pointer" wire:click='clearTemplateCaches'>
                            Clear Template Caches
                        </x-filament::link>
                    </div>

                    <div>
                        <x-filament::link class="text-blue-400 font-thin underline cursor-pointer">
                            Clear Scheduled Task Execution Logs
                        </x-filament::link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
