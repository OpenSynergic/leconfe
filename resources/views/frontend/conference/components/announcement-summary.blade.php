@props(['announcement'])

<div class="announcement-summary flex flex-wrap gap-x-2 p-1">
    @if ($announcement->hasMedia('featured_image'))
    <img class="max-h-36 w-auto"
        src="{{ $announcement->getFirstMedia('featured_image')->getAvailableUrl(['thumb']) }}" alt="">
    @endif
    <div class="leading-normal">
        <h3 class="text-lg tracking-tight text-gray-900 dark:text-white">
            <a href="{{ route('livewirePageGroup.conference.pages.announcement-page', ['announcement' => $announcement->id]) }}" class="link link-hover">
                {{ $announcement->title }}
            </a>    
        </h3>
        @php
            $announcementCreatedDate = $announcement->created_at->startOfDay();
            $diffInDays = $announcementCreatedDate->diffInDays(today());
        @endphp
        <p class="mb-3 text-xs font-medium text-gray-500 dark:text-gray-400">
            @if ($diffInDays > 0)
                Published {{ $diffInDays }} days ago
            @else
                Published today
            @endif
        </p>
        <p class="font-normal text-gray-700 dark:text-gray-400 text-xs">
            @if ($user = $announcement->user)
                {{ "By {$user->given_name} {$user->family_name}" }}
            @else
                By Admin
            @endif
        </p>
        @if ($announcement->tags_count)
            <div class="mt-1">
                @foreach ($announcement->tags as $tag)
                    <div class="badge badge-primary badge-outline text-xs keyword_tags">
                        {{ $tag->name }}
                    </div>
                @endforeach
                @if ($announcement->tags_count > 3)
                    <span class="text-xs">+ {{ $announcement->tags_count - 3 }}</span>
                @endif
            </div>
        @endif
    </div>
</div>


