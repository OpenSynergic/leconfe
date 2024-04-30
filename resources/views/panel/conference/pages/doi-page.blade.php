<x-filament-panels::page x-data="{ activeTab: 'submissions' }">
    <x-filament::tabs>
        <x-filament::tabs.item
            alpine-active="activeTab === 'submissions'"
            x-on:click="activeTab = 'submissions'"
            >
            Submissions
        </x-filament::tabs.item>

        <x-filament::tabs.item
            alpine-active="activeTab === 'proceedings'"
            x-on:click="activeTab = 'proceedings'"
        >
            Proceedings
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div x-show="activeTab === 'submissions'">
        Submission DOI Table
    </div>
    <div x-show="activeTab === 'proceedings'">
        @livewire(App\Panel\Conference\Livewire\ProceedingDOI::class)
    </div>

</x-filament-panels::page>
