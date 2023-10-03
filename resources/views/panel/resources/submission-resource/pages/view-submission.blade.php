<x-filament::page>
    @if ($this->record->status == App\Models\Enums\SubmissionStatus::Wizard)
        @livewire(App\Panel\Livewire\Wizards\SubmissionWizard::class, ['record' => $record])
    @else
    <div class="flex">
        <div class="flex-1">
            {{ $this->infolist }}
        </div>
        {{-- <div class="max-w-xs self-start sticky top-24"> --}}
            {{-- @livewire(App\Panel\Livewire\Submissions\SubmissionDetail\Decision::class, ['submission' => $record]) --}}
            {{-- @if($record->accepted() && $this->stageOpen('peer-review')) --}}
                {{-- Assign reviewer --}}
            {{-- @endifx --}}
        {{-- </div> --}}
    </div>
    @endif
</x-filament::page>
