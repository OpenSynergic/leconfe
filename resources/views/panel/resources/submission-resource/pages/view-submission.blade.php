<x-filament::page>
    @if ($this->record->status == App\Models\Submission::STATUS_WIZARD)
        <livewire:wizards.submission-wizard :record="$record" />
    @else
        <x-tabs>
            <x-slot:buttons>
                <x-tabs.button>Workflow</x-tabs.button>
                <x-tabs.button>Publication</x-tabs.button>
            </x-slot:buttons>

            <x-tabs.content>
                {{-- <livewire:panel.components.submissions.workflow-submission :record="$record" /> --}}
            </x-tabs.content>
            <x-tabs.content>
                publication
            </x-tabs.content>
        </x-tabs>
    @endif
</x-filament::page>
