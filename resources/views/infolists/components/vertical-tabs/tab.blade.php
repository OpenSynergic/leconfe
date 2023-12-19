@php
    $id = $getId();
    $isContained = $getContainer()->getParentComponent()->isContained();

    $visibleTabClasses = \Illuminate\Support\Arr::toCssClasses([
        'px-6' => $isContained,
        'mt-6' => ! $isContained,
    ]);

    $invisibleTabClasses = 'hidden p-0';
@endphp

<div
    x-bind:class="tab === @js($id) ? @js($visibleTabClasses) : @js($invisibleTabClasses)"
    {{ 
        $attributes
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
                'tabindex' => '0',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-tab-tabs-tab outline-none w-full'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
