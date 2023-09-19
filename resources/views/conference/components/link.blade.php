@props([
    'href' => '#',
    'spa' => true,
])

{{-- @php
    $spa = request()->segment(1) === config('app.filament.panel_path') ? false : $spa;
@endphp --}}

<a 
    {{ $attributes }}
    href="{{ $href }}"
    {{-- @if($spa)
        wire:navigate.hover
    @endif --}}
> 
    {{ $slot }}  
</a>

