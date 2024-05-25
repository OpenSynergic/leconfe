@php
    $isContained = $isContained();
@endphp

<div
    x-cloak
    x-data="{
        tab: null,

        init: function () {
            this.$watch('tab', () => this.updateQueryString())

            this.tab = @js(collect($getChildComponentContainer()->getComponents())
                        ->filter(static fn (\App\Infolists\Components\VerticalTabs\Tab $tab): bool => $tab->isVisible())
                        ->map(static fn (\App\Infolists\Components\VerticalTabs\Tab $tab) => $tab->getId())
                        ->values()
                        ->get($getActiveTab() - 1))
        },

        updateQueryString: function () {
            if (! @js($isTabPersistedInQueryString())) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(@js($getTabQueryStringKey()), this.tab)

            history.pushState(null, document.title, url.toString())
        },
    }"
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class([
                'flex flex-col xl:flex-row mb-5',
                'flex-row-reverse' => $getPosition() == 'right'
            ])
    }}
>
    <x-panel::vertical-tabs :contained="$isContained" :label="$getLabel()" :is-sticky="$isSticky()" :verticalSpace="$getVerticalSpace()">
        @foreach ($getChildComponentContainer()->getComponents() as $tab)
            @php
                $tabId = $tab->getId();
            @endphp

            <x-panel::vertical-tabs.item
                :alpine-active="'tab === \'' . $tabId . '\''"
                :badge="$tab->getBadge()"
                :icon="$tab->getIcon()"
                :icon-position="$tab->getIconPosition()"
                :x-on:click="'tab = \'' . $tabId . '\''"
            >
                {{ $tab->getLabel() }}
            </x-panel::vertical-tabs.item>
        @endforeach
    </x-panel::vertical-tabs>

    <div class="flex flex-col w-full mt-6 xl:mt-0">
    @foreach ($getChildComponentContainer()->getComponents() as $tab)
        {{ $tab }}
    @endforeach 
    </div>
</div>
