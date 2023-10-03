<div class="space-y-6">
    <div class="flex flex-col space-y-4">
        @if($submission->accepted())
            <div class="bg-primary-700 p-4 rounded-lg text-base">
                Your submission has been accepted. Now, we are waiting to next stage is open.
            </div>
        @endif
        {{ $this->acceptAction }}
        @if(!$submission->accepted())
            {{ $this->declineAction }}
        @endif
    </div>  
    <x-filament-actions::modals />
</div>