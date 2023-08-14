@props([
    'key',
])

<div x-navigation:dropdown-content="{{ $key }}"
    {{ 
        $attributes->twMerge([
            'p-1 mt-1 min-w-[10rem] bg-white rounded-md shadow-md ring-1 ring-gray-950/5 text-neutral-700'
        ]) 
    }}
>
    {{ $slot }}
</div>
