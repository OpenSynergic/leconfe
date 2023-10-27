<x-filament-panels::page>
    <div class="flex flex-col gap-y-10">
        <div class="flex flex-col gap-y-6 bg-white p-6">
            <div class="flex flex-col gap-y-4">
                <div class="flex flex-col gap-y-4">
                    <div class="w-52 border p-2 inline-flex items-center gap-1">
                        <x-heroicon-o-cog-6-tooth class="stroke-current shrink-0 h-6 w-6" />
                        <x-filament::link class="text-gray-700 font-thin cursor-pointer" wire:click='systemInformation'>
                            System Information
                        </x-filament::link>

                        <x-filament::modal id="edit-user">

                        </x-filament::modal>
                    </div>

                    <div class="w-52 border p-2 inline-flex items-center gap-1">
                        <x-feathericon-user-x class="stroke-current shrink-0 h-6 w-6" />
                        <x-filament::link class="text-gray-700 font-thin cursor-pointer" wire:click='expireUserSession'>
                            Expire User Session
                        </x-filament::link>
                    </div>

                    <x-filament::modal id="close"
                    >
                        <x-slot name="trigger">
                            <div class="w-52 border p-2 inline-flex items-center gap-1">
                                <x-heroicon-o-circle-stack class="stroke-current shrink-0 h-6 w-6" />
                                <x-filament::link class="text-gray-700 font-thin cursor-pointer"
                                    wire:click='clearDataCache'>
                                    Clear Data Caches
                                </x-filament::link>
                            </div>
                        </x-slot>

                        <div class="flex flex-col gap-4">
                            <div class="inline-flex gap-2 items-center">
                                <x-heroicon-o-exclamation-triangle class="stroke-current shrink-0 h-6 w-6 text-red-600" />
                                <h2 class="text-base font-medium text-red-600">You will clear your data caches</h2>
                            </div>
                            <p class="text-sm text-red-600">This action will clear all your entire data caches in your application</p>
                            <div class="flex gap-2">
                                <x-filament::button color="danger" icon="heroicon-m-trash">
                                    Clear
                                </x-filament::button>

                                <x-filament::button outlined color="danger" wire:click='closeModal' >
                                    Cancel
                                </x-filament::button>
                            </div>
                        </div>




                    </x-filament::modal>

                    <div class="w-52 border p-2 inline-flex items-center gap-1">
                        <x-heroicon-o-trash class="stroke-current shrink-0 h-6 w-6" />
                        <x-filament::link class="text-gray-700 font-thin cursor-pointer"
                            wire:click='clearTemplateCaches'>
                            Clear Template Caches
                        </x-filament::link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
