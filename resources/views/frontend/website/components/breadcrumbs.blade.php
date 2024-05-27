@props([
    'breadcrumbs' => [],
])

@if(!empty($breadcrumbs))
<div {{ $attributes->class(['breadcrumbs text-sm bg-gray-50 px-2 py-4 ps-4 rounded-md']) }}>
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
            </x-website::link>
            @else
                {{ $label }}
            @endif
        </li>
        @endforeach
    </ul>
</div>
@endif