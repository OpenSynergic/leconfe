@props([
    'href' => '#',
    'spa' => true,
])

<a 
    {{ $attributes }}
    href="{{ $href }}"
    @if($spa)
        wire:navigate.hover
    @endif
> 
    {{ $slot }}  
</a>