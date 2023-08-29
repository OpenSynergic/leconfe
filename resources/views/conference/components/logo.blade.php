@props([
    'href' => url('/'),
])

@if ($currentConference->hasMedia('logo'))
    <x-conference::link :href="$href">
        <img src="{{ $currentConference->getFirstMediaUrl('logo','tenant') }}" alt="{{ $currentConference->name }}"
        class="max-h-12 w-auto">
    </x-conference::link>
@else
    <x-conference::link :href="$href" class="text-2xl font-semibold">
        {{ $currentConference->name }}
    </x-conference::link>
@endif
