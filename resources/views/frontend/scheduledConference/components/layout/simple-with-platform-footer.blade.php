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

    <div class="fi-simple-layout flex min-h-screen flex-col items-center">
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

        <div class="fi-simple-main-ctn flex w-full flex-grow items-center justify-center">
            <main
                @class([
                    'fi-simple-main my-16 w-full bg-white px-6 py-12 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl sm:px-12',
                    ($maxWidth instanceof Width) ? "fi-width-{$maxWidth->value}" : (is_string($maxWidth) ? (Width::tryFrom($maxWidth) ? "fi-width-{$maxWidth}" : $maxWidth) : 'fi-width-lg'),
                ])
            >
                {{ $slot }}
            </main>
        </div>

        <x-footer-platform-panel />
    </div>
</x-filament-panels::layout.base>
