<x-website::layouts.main>
    <div class="p-5 space-y-2">
        <h2 class="text-heading">{{ 'Announcements' }}</h2>
        <div class="divide-y overflow-y-auto space-y-2">
            @forelse ($announcements as $announcement)
                <a href="{{ route('livewirePageGroup.current-conference.pages.announcement-page', ['announcement' => $announcement->id]) }}"
                    class="flex w-full bg-white md:flex-row hover:bg-gray-100 gap-x-2 p-1 group">
                    @if ($featuredImage = $announcement->getFirstMedia('featured_image'))
                        <img class="object-cover h-28 aspect-square"
                            src="{{ $featuredImage->getAvailableUrl(['thumb']) }}" alt="">
                    @endif
                    <div class="leading-normal">
                        <h3 class="text-lg tracking-tight text-gray-900 dark:text-white">{{ $announcement->title }}</h3>
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
                </a>
            @empty
                <div>
                    No Announcements created yet.
                </div>
            @endforelse
        </div>
    </div>
</x-website::layouts.main>
