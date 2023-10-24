<x-filament::section heading="Confiration Publishing">
    <div class="space-y-4">
        {{ $this->infolist }}
        {{ $this->publishAction() }}
    </div>
    <x-filament-actions::modals />
</x-filament::section>