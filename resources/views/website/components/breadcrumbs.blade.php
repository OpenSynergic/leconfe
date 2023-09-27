@props([
    'breadcrumbs' => [],
])

@if(!empty($breadcrumbs))
<div {{ $attributes->class(['breadcrumbs text-xs bg-gray-200 p-2 ps-4 rounded-md']) }}>
    <ul>
        @foreach ($breadcrumbs as $url => $label)
        <li>
            @if(!is_int($url))
            <x-website::link 
                :href="$url" 
                class="link link-hover link-primary"
                {{-- wire:navigate --}}
            >
                {{ $label }}
            </x-conference::link>
            @else
                {{ $label }}
            @endif
        </li>
        @endforeach
    </ul>
</div>
@endif