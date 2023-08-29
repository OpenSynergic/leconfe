<x-dynamic-component
    :component="$getFieldWrapperView()"
    :label="$getLabel()"
    :field="$field"
>
    <div>
        <div class="space-y-2">
            <span class="text-lg"{{ $getLabel() }}</span>
            <ul class="max-w-xs flex flex-col space-y-2" x-data="blockSortableContainer({
                state: $wire.$entangle('{{ $getStatePath() }}'),
                statePath: '{{ $getStatePath() }}',
            })">
        @if($getState())
                @foreach ($getState() as $uuid => $block)
                    <li
                        x-data="{
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
                            '-mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white hover:cursor-move'
                        ])>
                        <x-iconpark-drag class="w-5 h-5" />
                        {{ $block->name }}
                        <div class="ml-auto">
                            <input id="default-checkbox" type="checkbox" value="1" x-on:click="toggleStatus" {{ $block->active ? 'checked' : '' }}
                            @class([
                                'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500',
                                'dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600'
                            ])>
                        </div>
                    </li>
                @endforeach
            @endif
            </ul>
        </div>
    </div>
</x-dynamic-component>
