@if (filled($brand = filament()->getBrandName()))
    <div
        {{
            $attributes->class([
                'fi-logo text-xl font-bold leading-5 tracking-tight text-gray-700 dark:text-gray-200 flex items-center gap-4',
            ])
        }}
    >
    <img src="{{ Vite::asset('resources/assets/images/logo.png') }}" class="max-h-[2rem]" /> <span>{{ $brand }}</span>
</div>
@endif
