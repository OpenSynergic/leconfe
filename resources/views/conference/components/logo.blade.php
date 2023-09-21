@props([
    'href' => url('/'),
])

@if ($currentConference->hasMedia('logo'))
    <x-conference::link 
        {{ $attributes }}
        :href="$href"
    >
        <img src="{{ $currentConference->getFirstMedia('logo')->getAvailableUrl(['thumb','thumb-xl']) }}" alt="{{ $currentConference->name }}"
        class="max-h-12 w-auto">
    </x-conference::link>
@else
    <x-conference::link 
        :href="$href" 
        {{ $attributes->merge(['class' => 'text-lg sm:text-lg']) }}
    >
        {{ $currentConference->name }}
    </x-conference::link>
@endif
