@php
    $profileLinks = collect(config('app.contributor_profile_links', []))
        ->map(fn (array $profile): array => [
            ...$profile,
            'url' => $person->getMeta($profile['meta']),
        ])
        ->filter(fn (array $profile): bool => filled($profile['url']));
@endphp

@if ($profileLinks->isNotEmpty())
    <div class="cf-contributor-profile-links flex flex-wrap items-center gap-1">
        @foreach ($profileLinks as $profile)
            <a href="{{ $profile['url'] }}" target="_blank">
                <x-dynamic-component
                    :component="$profile['icon']"
                    class="contributor-profile-logo"
                    style="--contributor-profile-color: {{ $profile['color'] }}"
                />
            </a>
        @endforeach
    </div>
@endif
