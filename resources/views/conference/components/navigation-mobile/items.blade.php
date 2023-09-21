@props([
    'items' => [],
    'level' => 0,
])
<ul role="list" 
    {{ 
        $attributes->twMerge('space-y-2') 
    }}
    >
    @foreach ($items as $key => $item)
            @php($navigationLink = get_navigation_link($item['type'], $item['data']['url'] ?? '#'))
            <li class="relative">
                @if ($level >= 1)
                    <x-conference::link @class([
                        'block w-full pl-3.5 before:pointer-events-none before:absolute before:-left-1 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-y-1/2 before:rounded-full hover:before:block',
                        'font-semibold text-primary before:bg-primary' => request()->url() === $navigationLink,
                        'before:hidden before:bg-slate-300 text-slate-500 hover:text-slate-600' => request()->url() !== $navigationLink,
                    ]) :href="get_navigation_link($item['type'], $item['data']['url'] ?? '#')">
                        {{ $item['label'] }}
                    </x-conference::link>
                @else
                    <x-conference::link @class([
                        'text-primary font-semibold' => request()->url() === $navigationLink,
                        'text-slate-900 font-medium' => request()->url() !== $navigationLink,
                    ]) :href="get_navigation_link($item['type'], $item['data']['url'] ?? '#')">
                        {{ $item['label'] }}
                    </x-conference::link>
                @endif
            </li>
            @if (!empty($item['children']))
                <x-conference::navigation-mobile.items 
                        :items="$item['children']" 
                        :level="$level + 1"
                        @class([
                            'mt-2 space-y-2 border-l-2 border-slate-100 lg:mt-4 lg:space-y-4 lg:border-slate-200',
                            'ml-4' => $level >= 1,
                        ]) 
                    />
            @endif
    @endforeach
</ul>
