<x-conference::layouts.main>
        <div class="card-body">
            <h1 class="text-2xl mb-2 ml-2 text-gray-900 font-medium">{{ "Announcement" }}</h1>
            <div class="divide-y divide-dashed">
                @foreach ($announcementList as $announcement)
                    <a href="{{ route('livewirePageGroup.current-conference.pages.announcement-page', ['id' => $announcement->id]) }}" class="flex flex-col items-center bg-white md:flex-row hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                        @if ($announcement->getFirstMediaUrl())
                            <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-l-lg" src="{{ $announcement->getFirstMediaUrl() }}" alt="">
                        @endif
                        <div class="flex flex-col justify-between p-4 leading-normal">
                            <h5 class=" text-xl tracking-tight text-gray-900 dark:text-white">{{ $announcement->title }}</h5>
                            @php
                                $userId = $announcement->getMeta('author') ?? 0;
                                $user = App\Models\User::where('id', $userId)->first();
                                $announcementCreatedDate = $announcement->created_at->startOfDay();
                            @endphp
                            <p class="mb-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $announcementCreatedDate->diffInDays($currentDate) == 0 ? "Published today" : "Published {$announcementCreatedDate->diffInDays($currentDate)} days ago" }}</p>
                            <p class="font-normal text-gray-700 dark:text-gray-400 text-xs">By {{ $user ? "{$user->given_name} {$user->family_name}" : 'system' }}</p>
                            <div class="mt-2">
                                @foreach ($announcement->tags()->limit(3)->get() as $tag)
                                    <div class="badge badge-outline text-xs text-gray-500 keyword_tags"><span class="text-gray-900">{{ $tag->name }}</span></div>
                                @endforeach
                                @if ($announcement->tags()->count() > 3)
                                    <span class="text-xs">+ {{ $announcement->tags()->count() - 3 }}</span>                                    
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
</x-conference::layouts.main>