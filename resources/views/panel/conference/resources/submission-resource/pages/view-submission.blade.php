<x-filament::page>
    @if ($this->record->stage == App\Models\Enums\SubmissionStage::Wizard)
        @livewire(App\Panel\Conference\Livewire\Wizards\SubmissionWizard::class, ['record' => $record])
    @else
        {{ $this->infolist }}
    @endif
</x-filament::page>
