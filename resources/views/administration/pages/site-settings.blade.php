<x-filament-panels::page>
    {{ $this->infolist }}
    {{-- <div class="flex flex-col gap-y-6" x-data="{ activeTab: 'About' }">
        <x-filament::tabs>
            <x-filament::tabs.item alpine-active="activeTab === 'About'" x-on:click="activeTab = 'About'">
                About
            </x-filament::tabs.item>
            <x-filament::tabs.item alpine-active="activeTab === 'Setup'" x-on:click="activeTab = 'Setup'">
                Setup
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div x-show="activeTab === 'About'" x-data="{ activeVerticalTab: 'Information' }" class="flex flex-col xl:flex-row mb-5 gap-4">
            <x-panel::vertical-tabs>
                <x-panel::vertical-tabs.item 
                    alpine-active="activeVerticalTab == 'Information'"
                    x-on:click="activeVerticalTab = 'Information'"
                >
                    Information
                </x-panel::vertical-tabs.item>
            </x-panel::vertical-tabs>
            <div class="flex flex-col w-full mt-6 xl:mt-0">
                <div x-show="activeVerticalTab == 'Information'">
                    test
                </div>
            </div>
        </div>
        <div x-show="activeTab === 'Setup'" style="display: none">
            {{ $this->setupForm }}
        </div>
    </div> --}}
</x-filament-panels::page>
