@php
    use App\Panel\Conference\Livewire\Submissions\Components;
    use App\Models\Enums\SubmissionStage;
    use App\Constants\SubmissionFileCategory;
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="space-y-4 col-span-8">
            {{ $this->form }}
        </div>
        <div class="self-start sticky top-24 flex flex-col gap-3 col-span-4">
           side
        </div>
    </div>
    <x-filament-actions::modals />
</div>