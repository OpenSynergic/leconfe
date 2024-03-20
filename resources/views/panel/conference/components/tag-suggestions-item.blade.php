@props([
    'alpineValid' => null,
    'valid' => true,
])

@php
    $hasAlpineValidClasses = filled($alpineValid);

    $validInputClasses = 'text-primary-600 ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50 dark:ring-white/20 dark:checked:bg-primary-500 dark:focus:ring-primary-500 dark:checked:focus:ring-primary-400/50 dark:disabled:ring-white/10';
    $invalidInputClasses = 'text-danger-600 ring-danger-600 focus:ring-danger-600 checked:focus:ring-danger-500/50 dark:ring-danger-500 dark:checked:bg-danger-500 dark:focus:ring-danger-500 dark:checked:focus:ring-danger-400/50';
@endphp

<label class="relative inline-flex items-center cursor-pointer">
    <input
        type="checkbox"
        @if ($hasAlpineValidClasses)
            x-bind:class="{
                @js($validInputClasses): {{ $alpineValid }},
                @js($invalidInputClasses): {{ "(! {$alpineValid})" }},
            }"
        @endif
        {{
            $attributes
                ->class([
                    'sr-only peer',
                    $validInputClasses => (! $hasAlpineValidClasses) && $valid,
                    $invalidInputClasses => (! $hasAlpineValidClasses) && (! $valid),
                ])
        }}
    />
    <div 
        class="hover:ring-sky-500 fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] text-gray-500 bg-gray-50 peer-checked:bg-sky-50 peer-checked:text-sky-600 ring-sky-600/10 dark:bg-sky-400/10 dark:text-sky-400 dark:ring-sky-400/30"
    >
        {{ $slot }}
        <x-filament::icon
            alias="badge.delete-button"
            icon="heroicon-m-x-mark"
            class="h-3.5 w-3.5"
        />
    </div>
</label>
