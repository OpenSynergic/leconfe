<x-filament-panels::page @class([
    'fi-resource-list-records-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <div class="flex flex-col gap-y-6" x-data="{ activeTab: 'informations' }">
        <x-filament::tabs>
            <x-filament::tabs.item alpine-active="activeTab === 'informations'"
                x-on:click="activeTab = 'informations'">
                Informations
            </x-filament::tabs.item>
            <x-filament::tabs.item alpine-active="activeTab === 'notifications'"
                x-on:click="activeTab = 'notifications'">
                Notifications
            </x-filament::tabs.item>

        </x-filament::tabs>

        <div x-show="activeTab === 'informations'">
            {{ $this->informationForm }}
        </div>
        <div x-show="activeTab === 'notifications'" style="display: none">
            {{ $this->notificationForm }}
        </div>
    </div>
</x-filament-panels::page>
