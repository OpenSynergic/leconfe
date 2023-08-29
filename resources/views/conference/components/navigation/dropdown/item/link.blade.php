@props([
    'url' => '#',
    'icon' => null,
    'label',
])

<a href="{{ $url }}"
    class="relative flex hover:bg-neutral-100 items-center py-1.5 px-4 pr-6 text-sm outline-none transition-colors gap-4">
    @if ($icon)
        <div>{{ $icon }}</div>
    @endif
    <span>{{ $label }}</span>
</a>
