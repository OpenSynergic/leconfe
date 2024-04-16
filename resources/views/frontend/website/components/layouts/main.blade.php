@props([
    'sidebar' => true,
])

<div @class(['page-main'])>
    <div @class(['page-content', 'lg:col-span-9' => $sidebar, 'lg:col-span-full' => !$sidebar])>
        {{ $slot }}
    </div>

    {{-- TODO : change this implementation to check if there's sidebar enabled --}}
    @if ($sidebar)
        <x-website::layouts.rightbar />
    @endif
</div>
