<x-website::layouts.main>
    <div class="p-5">
        @can('update', $announcement)
            <div class="flex">
                <a class="ms-auto btn btn-primary btn-xs" href="{{ route('filament.panel.resources.conferences.announcements.edit', ['tenant' => $currentConference->path, 'record' => $announcement->getKey()]) }}"><x-heroicon-s-pencil-square class="h-4 w-4" /> Edit</a>
            </div>
        @endcan
        <div class="text-xs text-gray-500 font-medium">
            {{ $this->announcement->created_at->format(setting('format.date')) }}</div>
        <h1 class="card-title">{{ $this->announcement->title }}</h1>
        <div class="announcement-information">
            @php
                $announcementCreatedDate = $announcement->created_at->startOfDay();
                $diffInDays = $announcementCreatedDate->diffInDays(today());
            @endphp
            <div class="flex flex-wrap justify-between">
                <p class="text-xs text-gray-500">
                    @if ($diffInDays > 0)
                        Published {{ $diffInDays }} days ago
                    @else
                        Published today
                    @endif
                </p>
                <p class="text-gray-500 text-xs">
                    @if ($user = $announcement->user)
                        By {{ $user->fullName }}
                    @else
                        By Admin
                    @endif
                </p>
            </div>
        </div>
        <div class="user-content">
            {{ new Illuminate\Support\HtmlString($this->announcement->getMeta('content')) }}
        </div>
    </div>
    </x-conference::layouts.main>
