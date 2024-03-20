<x-filament-panels::sidebar.item
:active-icon="$item->getActiveIcon()"
:active="$item->isActive()"
:badge-color="$item->getBadgeColor()"
:badge="$item->getBadge()"
:first="$loop->first"
:grouped="filled($label)"
:icon="$item->getIcon()"
:last="$loop->last"
:should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
:url="$item->getUrl()"
>
{{ $item->getLabel() }}
</x-filament-panels::sidebar.item>