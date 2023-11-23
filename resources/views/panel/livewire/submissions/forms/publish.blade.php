<x-filament::section heading="Confiration Publishing">
    <div class="space-y-4">
        {{ $this->infolist }}
        @if($submission->stage == App\Models\Enums\SubmissionStage::Editing)
            {{ $this->publishAction() }}
        @endif
    </div>
    <x-filament-actions::modals />
</x-filament::section>