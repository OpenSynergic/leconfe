@props([
    'key',
])

<button x-navigation:trigger="{{ $key }}"
    {{ 
        $attributes->twMerge([
            'inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-200 focus:outline-none disabled:opacity-50 disabled:pointer-events-none group w-max'
        ]) 
    }}
>
    {{ $slot }}
</button>
