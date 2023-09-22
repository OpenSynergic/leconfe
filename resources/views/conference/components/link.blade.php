@props([
    'href' => '#',
    'spa' => true,
])

{{-- SPA Navigation disable, waiting stable from livewire --}}
{{-- 
@php
    $spa = request()->segment(1) === config('app.filament.panel_path') ? false : $spa;
@endphp 
--}}

<a 
    {{ $attributes }}
    href="{{ $href }}"
    {{-- @if($spa)
        wire:navigate.hover
    @endif --}}
> 
    {{ $slot }}  
</a>

