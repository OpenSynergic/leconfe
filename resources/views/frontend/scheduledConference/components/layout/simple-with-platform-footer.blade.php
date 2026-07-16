@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;
    $maxWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);
@endphp

@push('styles')
    @vite(['resources/panel/css/panel.css'])
@endpush

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout">
        @if (($hasTopbar ?? true) && filament()->auth()->check())
            <div class="fi-simple-layout-header">
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications()
                    ])
                @endif

                <x-filament-panels::user-menu />
            </div>
        @endif

        <div class="fi-simple-main-ctn">
            <main
                @class([
                    'fi-simple-main',
                    ($maxWidth instanceof Width) ? "fi-width-{$maxWidth->value}" : (is_string($maxWidth) ? (Width::tryFrom($maxWidth) ? "fi-width-{$maxWidth}" : $maxWidth) : 'fi-width-lg'),
                ])
            >
                {{ $slot }}
            </main>
        </div>

        <x-footer-platform-panel />
    </div>
</x-filament-panels::layout.base>
