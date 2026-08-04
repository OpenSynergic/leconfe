<x-website::layouts.main>
    <div class="mb-6">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
    </div>
    <div class="relative">
        <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-semibold min-w-fit">Event Timelines</h1>
                @if(!empty($currentScheduledConferenceTimezoneLabel))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $currentScheduledConferenceTimezoneLabel }}
                    </span>
                @endif
            </div>
            <hr class="w-full h-px my-auto bg-gray-200 border-0 dark:bg-gray-700">
        </div>
        @if($timelines->isNotEmpty())
        <ol class="relative border-s border-gray-200">
            @foreach ($timelines as $timeline)
                <li class="mb-10 ms-4 last:mb-0">
                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500" datetime="{{ $timeline->date?->toIso8601String() }}">
                        {{ $timeline->fullDate }} @if(!empty($currentScheduledConferenceTimezone))({{ $currentScheduledConferenceTimezone }})@endif
                    </time>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $timeline->name }}</h3>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">
                        {{ $timeline->description }}
                    </p>
                </li>
            @endforeach
        </ol>
        @else
            <div class="">
                No timelines yet.
            </div>
        @endif
    </div>
</x-website::layouts.main>
