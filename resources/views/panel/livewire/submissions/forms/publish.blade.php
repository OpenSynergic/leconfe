<x-filament::section heading="Confiration Publishing">
    <div class="space-y-4">
        {{ $this->infolist }}
        @can('publish', $submission)
            {{ $this->publishAction() }}
        @endcan
    </div>
    <x-filament-actions::modals />
</x-filament::section>