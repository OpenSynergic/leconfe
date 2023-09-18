<x-filament::page>
    @if ($this->record->status == App\Models\Enums\SubmissionStatus::Wizard)
        @livewire(App\Panel\Livewire\Wizards\SubmissionWizard::class, ['record' => $record])
    @else
        {{-- <x-filament::tabs>
            <x-filament::tabs.item>
                <x-tabs.button>Workflow</x-tabs.button>
                <x-tabs.button>Publication</x-tabs.button>
            </x-filament::tabs.item> --}}

            {{-- <x-tabs.content> --}}
                {{-- <livewire:panel.components.submissions.workflow-submission :record="$record" /> --}}
            {{-- </x-tabs.content>
            <x-tabs.content>
                publication
            </x-tabs.content>
        </x-filament::tabs> --}}
    @endif
</x-filament::page>
