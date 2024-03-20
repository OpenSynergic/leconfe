<div @if (filament()->isSidebarCollapsibleOnDesktop()) x-bind:class="$store.sidebar.isOpen ? '-mx-2' : '-mx-4'" @endif>
    <x-filament::dropdown placement="bottom-start" teleport>
        <x-slot name="trigger">
            <button type="button"
                class="fi-tenant-menu-trigger group flex w-full items-center justify-center gap-x-3 rounded-lg p-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5">

                <x-filament::icon icon="heroicon-m-cog-8-tooth" class="h-5 w-5" />

                 <span class="text-gray-950 dark:text-white text-lg">
                    {{-- Administration --}}
                </span>

                <x-filament::icon icon="heroicon-m-chevron-down" icon-alias="panels::tenant-menu.toggle-button"
                    :x-show="filament()->isSidebarCollapsibleOnDesktop() ? '$store.sidebar.isOpen' : null"
                    class="ms-auto h-5 w-5 shrink-0 text-gray-400 transition duration-75 group-hover:text-gray-500 group-focus:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-400 dark:group-focus:text-gray-400" />
            </button>
        </x-slot>
        <x-filament::dropdown.list>
            @foreach (auth()->user()->getTenants(filament()->getCurrentPanel()) as $tenant)
                <x-filament::dropdown.list.item :href="route('filament.conference.pages.dashboard', $tenant)" :image="filament()->getTenantAvatarUrl($tenant)" tag="a">
                    {{ filament()->getTenantName($tenant) }}
                    <x-filament::badge size="sm" class="text-[10px] w-fit" :color="$tenant->status->getColor()">
                        {{ $tenant->status }}
                    </x-filament::badge>
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
