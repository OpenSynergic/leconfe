<x-filament-panels::page>
    {{ $this->infolist }}
    <div class="ml-auto max-w-md flex space-x-3 ">
        @if($review->status == App\Constants\ReviewerStatus::PENDING)
            {{ $this->acceptAction() }}
        @endif
        @if($review->status == App\Constants\ReviewerStatus::PENDING)
            {{ $this->declineAction() }}
        @endif
    </div>
</x-filament-panels::page>
