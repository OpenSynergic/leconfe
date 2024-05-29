@if ($headerLogo)
    <x-website::link {{ $attributes }} :href="$homeUrl">
        <img
            src="{{ $headerLogo }}"
            alt="{{ $headerLogoAltText }}"
            class="max-h-12 w-auto"
        />
    </x-website::link>
@else
    <x-website::link
        :href="$homeUrl"
        {{ $attributes->merge(['class' => 'text-lg sm:text-lg text-primary-content']) }}
    >
        {{ $headerLogoAltText }}
    </x-website::link>
@endif