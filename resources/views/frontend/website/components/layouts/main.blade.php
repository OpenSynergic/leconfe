@props([
    'sidebars' => \App\Facades\SidebarFacade::get(),
    'sidebar' => null,
])

@php
    $sidebar ??= count($sidebars) > 0;
@endphp

<div @class(['page-main'])>
    <div @class(['page-content', 'lg:col-span-9' => $sidebar, 'lg:col-span-full' => !$sidebar])>
        {{ $slot }}
    </div>

    @if ($sidebar)
        <x-website::layouts.sidebar :sidebars="$sidebars"/>
    @endif
</div>
