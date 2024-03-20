@use('\App\Models\Conference')

@php
    $currentConference = app()->getCurrentConference();
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::tenant-menu.before') }}

<x-filament::dropdown
    placement="bottom-start"
    teleport
    id="switch-conference"
>
    <x-slot name="trigger">
        <button
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-data="{ tooltip: false }"
                x-effect="
                    tooltip = $store.sidebar.isOpen
                        ? false
                        : {
                              content: @js($currentConference->name),
                              placement: document.dir === 'rtl' ? 'left' : 'right',
                              theme: $store.theme,
                          }
                "
                x-tooltip.html="tooltip"
            @endif
            type="button"
            class="fi-tenant-menu-trigger group flex w-full items-center justify-center gap-x-3 rounded-lg p-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5"
        >
            <x-filament-panels::avatar.tenant :tenant="$currentConference"/>

            <span class="hidden md:grid justify-items-start text-start">
                <span class="text-gray-950 dark:text-white text-lg">
                    {{ $currentConference->name }}
                </span>

                <x-filament::badge size="sm" class="text-[10px]" :color="$currentConference->status->getColor()">
                    {{ $currentConference->status }}
                </x-filament::badge>                
                 
            </span>

             <x-filament::icon
                icon="heroicon-m-chevron-down"
                icon-alias="panels::tenant-menu.toggle-button"
                class="hidden md:block ms-auto h-5 w-5 shrink-0 text-gray-400 transition duration-75 group-hover:text-gray-500 group-focus:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-400 dark:group-focus:text-gray-400"
            />
        </button>
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            :href="route('filament.administration.home')"
            icon="heroicon-s-cog"
            tag="a"
        >
            {{ __('Administration') }}
        </x-filament::dropdown.list.item>

        @foreach (Conference::where('path', '!=', app()->getCurrentConference()->path)->get() as $conference)
            <x-filament::dropdown.list.item
                :color="$conference->status->getColor()"
                :href="$conference->getPanelUrl()"
                :icon="filament()->getTenantAvatarUrl($conference)"
                tag="a"
            >
                {{ $conference->name }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>

</x-filament::dropdown>

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::tenant-menu.after') }}