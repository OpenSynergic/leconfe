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
            @php($item = new App\Classes\NavigationItem(...$item))
            <li class="relative">
                @if ($level >= 1)
                    <x-website::link @class([
                        'block w-full pl-3.5 before:pointer-events-none before:absolute before:-left-1 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-y-1/2 before:rounded-full hover:before:block',
                        'font-semibold text-primary before:bg-primary' => request()->url() === $item->getUrl(),
                        'before:hidden before:bg-slate-300 text-slate-500 hover:text-slate-600' => request()->url() !== $item->getUrl(),
                    ]) :href="$item->getUrl()">
                        {{ $item->getLabel() }}
                    </x-website::link>
                @else
                    <x-website::link @class([
                        'text-primary font-semibold' => request()->url() === $item->getUrl(),
                        'text-slate-900 font-medium' => request()->url() !== $item->getUrl(),
                    ]) :href="$item->getUrl()">
                        {{ $item->getLabel() }}
                    </x-website::link>
                @endif
            </li>
            @if ($item->hasChildren())
                <x-website::navigation-mobile.items 
                        :items="$item->getChildren()" 
                        :level="$level + 1"
                        @class([
                            'mt-2 space-y-2 border-l-2 border-slate-100 lg:mt-4 lg:space-y-4 lg:border-slate-200',
                            'ml-4' => $level >= 1,
                        ]) 
                    />
            @endif
    @endforeach
</ul>
