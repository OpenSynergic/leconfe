<div class="space-y-4 sidebar-settings">
    <x-filament::section>
        <x-slot name="heading">
            {{ __('general.sidebars_manager') }}
        </x-slot>
        @if ($sidebars->isEmpty())
            <div>
                <p class="text-sm text-gray-500">
                    {{ __('general.no_sidebars_found') }}
                </p>
            </div>
        @else
            <div class="space-y-4" x-data="sidebarsManager({{ Js::from($sidebars) }})" wire:ignore>
                <div class="flex flex-col gap-2 text-sm sidebar-items" x-ref="sortable">
                    <template x-for="sidebar in items" :key="sidebar.id">
                        <div class="sidebar-item" data-sortable-item :data-id="sidebar.id">
                            <div class="relative group">
                                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl">
                                    <button type="button"
                                        class="p-3 text-sm text-gray-500 border-r border-gray-200 bg-gray-50 rounded-l-xl hover:text-gray-900"
                                        data-sortable-handle>
                                        <x-heroicon-s-arrows-up-down class="w-4 h-4" />
                                    </button>
                                    <div class="flex items-center gap-2 sidebar-item-name">
                                        <div x-html="sidebar.prefixName"></div>
                                        <div x-text="sidebar.name"></div>
                                        <div x-html="sidebar.suffixName"></div>
                                    </div>
                                    <x-filament::input.checkbox class="mx-4 ml-auto" x-model="sidebar.isActive" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <x-filament::button x-on:click="save" wire:target="save">
                    {{ __('general.save') }}
                </x-filament::button>
            </div>
        @endif
    </x-filament::section>
</div>
