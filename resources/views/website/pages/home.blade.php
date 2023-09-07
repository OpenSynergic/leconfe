<x-conference::layouts.main>
    <div class="card">
        <div class="card-body">
            <h2 class="card-title font-normal">Announcements</h2>
            <div class="flex flex-col space-y-2 rounded-sm border p-4">
                <div class="flex flex-col gap-2">
                    @foreach ($announcements as $announcement)
                        <h5 class="text-md font-medium">{{ $announcement->title }}</h5>
                        <p class="text-[.65em]">
                            {!! $announcement->announcement !!}
                        </p>
                    @endforeach
                </div>
                <div class="flex justify-end">
                    <div class="inline-flex gap-2 rounded-sm text-xs shadow-sm" role="group">
                        <button
                            class="btn btn-primary btn-sm text-xs font-normal normal-case rounded-md text-white">1</button>
                        <button
                            class="btn btn-primary btn-sm text-xs font-normal normal-case rounded-md btn-outline">2</button>
                        <button
                            class="btn btn-primary btn-outline btn-sm text-xs font-normal normal-case rounded-md">3</button>
                        <button class="btn btn-primary btn-sm text-xs font-normal normal-case text-white">Read
                            More...</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body space-y-2">
            <div class="conference-current space-y-2">
                <div class="flex justify-between">
                    <div class="card-title font-normal">Highlight Conference</div>
                    <div class="bg-gray-300 text-gray-800 badge badge-sm">{{ $currentConference->type ?? '' }}</div>
                </div>
                <div class="pb-4">
                    <span class="badge badge-outline">{{ $currentConference->getMeta('date_held') ?? '' }}</span>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    @if ($currentConference->hasMedia('thumbnail'))
                        <div class="cf-thumbnail sm:max-w-[12rem]">
                            <img class="w-full" src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                alt="{{ $currentConference->name }}">
                        </div>
                    @endif
                    <div class="cf-information space-y-2 w-full">
                        <div class="flex flex-wrap justify-between items-center -mt-2">
                            <h3 class="text-lg">{{ $currentConference->name ?? '' }}</h3>
                        </div>
                        <div class="cf-description prose text-[.85em]">
                            @if ($currentConference->getMeta('location'))
                                <p>{{ $currentConference->getMeta('location') }}
                            @endif
                            {!! $currentConference->getMeta('description') !!}
                        </div>

                        <div class="flex flex-col space-y-2">
                            <div class="flex gap-2 prose text-[.85em] flex-wrap max-w-[20rem]">
                                @foreach ($topics as $topic)
                                    @if ($topic)
                                        <span
                                            class="badge badge-outline badge-sm text-[.85em]">{{ $topic->name }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="flex justify-end flex-wrap">
                            <button
                                class="btn btn-primary btn-sm btn-primary text-white font-normal rounded-md">Enroll</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title font-normal">Upcoming Conferences</div>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach ($upcomings as $upcoming)
                        <div
                            class="bg-base card card-compact h-[330px] w-[220px] rounded-sm border border-gray-300 p-5">
                            <img src="{{ $upcoming->getFirstMediaUrl('thumbnail', 'thumb') }}" alt=""
                                class="rounded-sm object-top h-[150px]" />
                            <div class="card-body">
                                <div class="-mx-4 grid gap-4">
                                    <h2 class="card-title text-sm font-medium ">{{ $upcoming->name }}</h2>
                                    @if ($upcoming->hasMeta('date_held'))
                                        <p class="-mt-3 text-[.75em]">Enrollment start on,
                                            {{ $upcoming->getMeta('date_held') }}</p>
                                    @endif
                                </div>
                                <div class="card-actions justify-end">
                                    <button
                                        class="btn btn-primary btn-outline btn-sm rounded-sm text-[.75em] font-normal normal-case rounded">Enrol</button>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
            <div class="grid place-items-end">
                <button class="btn btn-primary btn-sm text-white font-normal text-xs">See All</button>
            </div>
        </div>
    </div>



</x-conference::layouts.main>
