<x-filament-panels::page>
    <div class="navigation-menus space-y-4 max-w-screen-md">
        @forelse ($navigationMenus as $navigationMenu)
            <div class="navigation-menu bg-white rounded-lg border">
                <div class="flex items-center flex-wrap p-4 gap-2">
                    <div>
                        <h2 class="text-base font-medium leading-none">{{ $navigationMenu->name }}</h2>
                        <span class="text-sm text-gray-500">{{ $navigationMenu->handle }}</span>
                    </div>
                    <div class="ml-auto flex flex-wrap items-center gap-2">
                        {{ ($this->editNavigationMenuAction)(['id' => $navigationMenu->id]) }}
                        {{ ($this->deleteNavigationMenuAction)(['id' => $navigationMenu->id]) }}
                    </div>
                </div>
                <hr />
                <div class="navigation-menu-items text-sm space-y-2 p-4" x-data="navigationMenuItemSortable({
                        group: {{ $navigationMenu->id }},
                        parentId: null
                    })">
                    @forelse ($navigationMenu->items as $navigationMenuItem)
                        <div class="space-y-2" data-sortable-item data-id="{{ $navigationMenuItem->id }}" x-data="{ open: true }">
                            <div class="relative group">
                                <div class="flex items-center gap-4 rounded-xl border bg-white">
                                    <button type="button"
                                        class="text-sm p-3 bg-gray-50 rounded-l-xl text-gray-500 hover:text-gray-900 border-r"
                                        data-sortable-handle>
                                        <x-heroicon-s-arrows-up-down class="h-4 w-4" />
                                    </button>
                                    <button class="flex cursor-pointer" wire:click="mountAction('editNavigationMenuItemAction', {{ Js::from(['id' => $navigationMenuItem->id]) }})">
                                        {{ $navigationMenuItem->label }}
                                        {{ ($this->editNavigationMenuItemAction)(['id' => $navigationMenuItem->id]) }}
                                    </button>
                                    @if($navigationMenuItem->children->isNotEmpty())
                                    <button type="button" x-on:click="open = !open" title="Toggle children" class="appearance-none text-gray-500">
                                        <x-heroicon-c-chevron-down class="w-3.5 h-3.5 transition ease-in-out duration-200" x-bind:class="!open && '-rotate-180'"/>
                                    </button>
                                    @endif
                                </div>
                                <div
                                    class="absolute top-0 right-0 h-6 divide-x rounded-bl-lg rounded-tr-lg border-gray-300 border-b border-l overflow-hidden rtl:border-l-0 rtl:border-r rtl:right-auto rtl:left-0 rtl:rounded-bl-none rtl:rounded-br-lg rtl:rounded-tr-none rtl:rounded-tl-lg hidden opacity-0 group-hover:opacity-100 group-hover:flex transition ease-in-out duration-250 dark:border-gray-600 dark:divide-gray-600">
                                    {{ $this->deleteNavigationItemMenuAction }}
                                    {{ $this->addNavigationMenuItemChildAction }}
                                    <button 
                                        x-tooltip.raw.duration.0="Add child" 
                                        type="button" 
                                        wire:click="mountAction('addNavigationMenuItemChildAction', {{ Js::from(['navigation_menu_id' => $navigationMenu->id, 'parent_id' => $navigationMenuItem->id]) }})" 
                                        class="p-1"
                                        title="Add child">
                                        <svg class="w-3 h-3 text-gray-500 hover:text-gray-900"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15">
                                            </path>
                                        </svg> 
                                    </button>

                                    <button 
                                        x-tooltip.raw.duration.0="Remove" 
                                        type="button"
                                        class="p-1" 
                                        wire:click="mountAction('deleteNavigationItemMenuAction', {{ Js::from(['id' => $navigationMenuItem->id]) }})" 
                                        title="Remove">
                                        <svg class="w-3 h-3 text-danger-500 hover:text-danger-900"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                            </path>
                                        </svg> 
                                    </button>
                                </div>
                            </div>
                            {{-- @if($navigationMenuItem->children->isNotEmpty()) --}}
                            <div x-show="open" x-collapse class="pl-10 space-y-2" x-data="navigationMenuItemSortable({
                                    group: {{ $navigationMenu->id }},
                                    parentId: {{ $navigationMenuItem->id }}
                                })">
                                @foreach($navigationMenuItem->children as $child)
                                    <div class="relative group" data-sortable-item data-id="{{ $child->id }}">
                                        <div class="flex items-center gap-4 rounded-xl border bg-white">
                                            <button type="button"
                                                class="text-sm p-3 bg-gray-50 rounded-l-xl text-gray-500 hover:text-gray-900 border-r"
                                                data-sortable-handle>
                                                <x-heroicon-s-arrows-up-down class="h-4 w-4" />
                                            </button>
                                            <button 
                                                class="flex cursor-pointer" 
                                                wire:click="mountAction('editNavigationMenuItemAction', {{ Js::from(['id' => $child->id]) }})">
                                                {{ $child->label }}
                                                {{ $this->editNavigationMenuItemAction }}
                                            </button>
                                        </div>
                                        <div
                                            class="absolute top-0 right-0 h-6 divide-x rounded-bl-lg rounded-tr-lg border-gray-300 border-b border-l overflow-hidden rtl:border-l-0 rtl:border-r rtl:right-auto rtl:left-0 rtl:rounded-bl-none rtl:rounded-br-lg rtl:rounded-tr-none rtl:rounded-tl-lg hidden opacity-0 group-hover:opacity-100 group-hover:flex transition ease-in-out duration-250 dark:border-gray-600 dark:divide-gray-600">
                                            {{ $this->deleteNavigationItemMenuAction }}
                                            <button 
                                                x-tooltip.raw.duration.0="Remove" 
                                                type="button"
                                                class="p-1" 
                                                wire:click="mountAction('deleteNavigationItemMenuAction', {{ Js::from(['id' => $child->id]) }})" 
                                                title="Remove">
                                                <svg class="w-3 h-3 text-danger-500 hover:text-danger-900"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                    </path>
                                                </svg> 
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{-- @endif --}}
                        </div>
                    @empty
                        <div class="text-gray-500">No Navigation Menu items found</div>
                    @endforelse
                </div>
                <div class="flex items-center p-4 pt-0">
                    <div class="ml-auto">
                        {{ ($this->addNavigationMenuItemAction)(['navigation_menu_id' => $navigationMenu->id]) }}
                    </div>
                </div>
            </div>
        @empty
            <div>
                No Navigation Menu found
            </div>
        @endforelse
        {{-- <x-filament-actions::modals /> --}}
    </div>

</x-filament-panels::page>
