<x-dynamic-component :component="$getFieldWrapperView()" :label="$getLabel()" :field="$field">
    <div>
        <div class="space-y-2">
            <ul class="flex flex-col space-y-2" x-data="blockSortableContainer({
                state: $wire.$entangle('{{ $getStatePath() }}'),
                statePath: '{{ $getStatePath() }}',
            })">
                @if (!$getState())
                    <div>
                        <!-- component -->
                        <div
                            class="border-dashed border-2 dark:border-gray-800 w-64 h-32 rounded flex justify-center items-center">
                            <span class="block text-grey">
                                <span class="text-sm text-gray-600">Place the block here.</span>
                            </span>
                        </div>
                    </div>
                @endif
                @if ($getState())
                    @foreach ($getState() as $uuid => $block)
                        <li x-data="{
                            toggleStatus() {
                                const oldAttribute = $el.getAttribute('data-id')
                                const newAttribute = oldAttribute.includes(':enabled') ? oldAttribute.replace(':enabled', ':disabled') : oldAttribute.replace(':disabled', ':enabled')
                                $el.setAttribute('data-id', newAttribute)
                                update()
                            }
                        }"
                            data-id="{{ $uuid . ':' . ($block->active ? 'enabled' : 'disabled') . ':' . $getStatePath() }}"
                            @class([
                                'inline-flex rounded-md items-center gap-x-2 py-3 px-4 text-sm font-medium bg-white border text-gray-800',
                                '-mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white hover:cursor-move',
                            ])>
                            <x-iconpark-drag class="w-5 h-5" />
                            <div class="block-information">
                                @if ($block->prefix)
                                    {!! $block->prefix !!}
                                @endif
                                <span>{{ $block->name }}</span>
                                @if ($block->suffix)
                                    {!! $block->suffix !!}
                                @endif
                            </div>
                            <div class="ml-auto">
                                <input id="default-checkbox" type="checkbox" value="1" x-on:click="toggleStatus"
                                    {{ $block->active ? 'checked' : '' }} 
                                    @class([
                                        'w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-blue-500',
                                        'focus:ring-2 dark:bg-gray-700 dark:border-gray-600',
                                    ])
                                    >
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
</x-dynamic-component>
