<x-filament-panels::page>
    <div class="flex flex-col gap-y-6" x-data="{ activeTab: 'Proceeding Data' }" x-cloak>
        <x-filament::tabs class="">
            <x-filament::tabs.item 
                alpine-active="activeTab === 'Proceeding Data'"
                x-on:click="activeTab = 'Proceeding Data'"
                >
                Proceeding Data
            </x-filament::tabs.item>
            <x-filament::tabs.item 
                alpine-active="activeTab === 'Articles'"
                x-on:click="activeTab = 'Articles'"
                >
                Articles
            </x-filament::tabs.item>
        </x-filament::tabs>
        <div x-show="activeTab === 'Proceeding Data'">
            <form wire:submit='submit' class="space-y-4">
                <div class="p-4 bg-white rounded-xl ring-1 ring-gray-950/5">
                    {{ $this->form }}
                </div>

                @can('update', $this->getRecord())
                <x-filament::button type="submit" icon="iconpark-save-o">
                    Save
                </x-filament::button>
                @endcan
            </form>
        </div>
        <div x-show="activeTab === 'Articles'">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
