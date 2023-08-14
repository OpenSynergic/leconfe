@props([
    'key',
])

<button x-navigation:trigger="{{ $key }}"
    {{ 
        $attributes->twMerge([
            'btn btn-ghost btn-sm rounded-full inline-flex items-center justify-center px-4 transition-colors hover:text-primary-content focus:outline-none disabled:opacity-50 disabled:pointer-events-none group w-max'
        ]) 
    }}
>
    {{ $slot }}
</button>
