@props([
    'items' => []
])

<div {{ 
    $attributes->twMerge([
        'flex flex-col divide-y p-1 mt-1 min-w-[12rem] bg-white rounded-md shadow-md text-neutral-700'
    ])
}}>
    @foreach ($items as $key => $item)
        @if (!empty($item['children']))
            <x-website::navigation.dropdown.item.has-children :item="$item"/>
            @continue
        @endif

        @switch($item['type'])
            @case('external-link')
                <x-website::navigation.dropdown.item.link class="w-full text-left p-0" :label="$item['label']"
                    :url="$item['data']['url']" />
            @break

            @default
                <x-website::navigation.dropdown.item.link class="w-full text-left p-0" :label="$item['label']" />
            @break
        @endswitch
    @endforeach
</div>