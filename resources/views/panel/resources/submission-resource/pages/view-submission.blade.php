<x-filament::page>
    @if ($this->record->status == App\Models\Enums\SubmissionStatus::Wizard)
        @livewire(App\Panel\Livewire\Wizards\SubmissionWizard::class, ['record' => $record])
    @else
    <div class="flex">
        <div class="flex-1">
            {{ $this->infolist }}
        </div>
    </div>
    @endif
</x-filament::page>
