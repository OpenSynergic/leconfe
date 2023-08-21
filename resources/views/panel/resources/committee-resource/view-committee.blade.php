<x-filament::page>
    <x-filament::tabs>
        <x-filament::tabs.item
        :active="$activeTab === 'tab1'"
        wire:click="$set('activeTab', 'tab1')"
        >
            Members

        </x-filament::tabs.item>

        <x-filament::tabs.item
        :active="$activeTab === 'tab2'"
        wire:click="$set('activeTab', 'tab2')">
           Structure
        </x-filament::tabs.item>

    </x-filament::tabs>

    @if ($activeTab === 'tab1')
    <livewire:committee-member-table/>
    @endif

    <!-- Konten Tab 2 -->
    @if ($activeTab === 'tab2')
    <livewire:committee-table/>

    @endif


</x-filament::page>
