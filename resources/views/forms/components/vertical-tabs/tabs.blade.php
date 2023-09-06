@php
    $isContained = $isContained();
@endphp
<div
    wire:ignore.self
    x-cloak
    x-data="{
        tab: null,

        init: function () {
            this.$watch('tab', () => this.updateQueryString())

            this.tab = this.getTabs()[@js($getActiveTab()) - 1]
        },

        getTabs: function () {
            return JSON.parse(this.$refs.tabsData.value)
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
                'wire:key' => "{$this->getId()}.{$getStatePath()}." . \App\Forms\Components\VerticalTabs\Tabs::class . '.container',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class([
                'flex flex-col xl:flex-row mb-5',
                'flex-row-reverse' => $getPosition() == 'right'
                // 'fi-fo-tabs flex flex-col',
                // 'fi-contained rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => $isContained,
            ])
    }}
>
    <input
        type="hidden"
        value="{{
            collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (\App\Forms\Components\VerticalTabs\Tab $tab): bool => $tab->isVisible())
                ->map(static fn (App\Forms\Components\VerticalTabs\Tab $tab) => $tab->getId())
                ->values()
                ->toJson()
        }}"
        x-ref="tabsData"
    />

    <x-panel::vertical-tabs :contained="$isContained" :label="$getLabel()" :is-sticky="$isSticky()">
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
