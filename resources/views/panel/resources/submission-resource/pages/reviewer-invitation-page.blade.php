<x-filament-panels::page>
    {{ $this->infolist }}
    <div class="ml-auto max-w-md flex space-x-3 ">
        {{ $this->acceptAction() }}
        {{ $this->declineAction() }}
    </div>
</x-filament-panels::page>
