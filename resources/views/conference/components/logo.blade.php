@props([
    'url' => url('/'),
])

@if ($currentConference->hasMedia('logo'))
    <a href="{{ $url }}">
        <img src="{{ $currentConference->getFirstMediaUrl('logo','tenant') }}" alt="{{ $currentConference->name }}"
            class="max-h-12 w-auto">
    </a>
@else
    <a class="text-2xl font-semibold" href="{{ $url }}">{{ $currentConference->name }}</a>
@endif
