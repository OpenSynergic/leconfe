<x-conference::layouts.main>
    <div class="card-body">
        <h1 class="text-xl mb-2 ml-2 text-gray-900">{{ 'Announcements' }}</h1>
        <div class="divide-y overflow-y-auto">
            @foreach ($this->records as $announcement)
                <a href="{{ route('livewirePageGroup.current-conference.pages.announcement-page', ['announcement' => $announcement->id]) }}"
                    class="flex flex-col bg-white md:flex-row hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                    @if ($featuredImage = $announcement->getFirstMedia('featured_image'))
                        <img class="object-cover h-28 aspect-square mt-4 mb-4"
                            src="{{ $featuredImage->getAvailableUrl(['thumb']) }}" alt="">
                    @endif
                    <div class="flex flex-col justify-between p-4 leading-normal">
                        <h5 class=" text-lg tracking-tight text-gray-900 dark:text-white">{{ $announcement->title }}</h5>
                        @php
                            $announcementCreatedDate = $announcement->created_at->startOfDay();
                            $diffInDays = $announcementCreatedDate->diffInDays($currentDate);
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
                                    <div class="badge badge-outline text-xs text-gray-500 keyword_tags"><span
                                            class="text-gray-900">{{ $tag->name }}</span></div>
                                @endforeach
                                @if ($announcement->tags_count > 3)
                                    <span class="text-xs">+ {{ $announcement->tags_count - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-conference::layouts.main>
