<x-conference::layouts.main>
    <div class="px-6 py-2 mt-6">
        <p class="prose prose-lg text-secondary">Highlight Conference</p>
    </div>

    <div class="card px-5 py-3 -mt-2">
        <div class="card-body space-y-2 border rounded">
            <div class="cf-current p-4 -mt-1">
                <p class="prose prose-lg text-secondary -mt-2">{{ $currentConference->name }}</p>
                @if ($currentConference->hasMeta('date_held'))
                    <div class="inline-flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                        <time class="prose text-xs text-secondary">{{ $currentConference->getMeta('date_held') }}</time>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row space-x-4">
                    @if ($currentConference->hasMedia('thumbnail'))
                        <div class="cf-thumbnail sm:max-w-[12rem]">
                            <img class="h-full w-full rounded object-cover sm:object-left-top"
                                src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}" alt="" />
                        </div>
                    @endif
                    <div class="flex flex-col gap-2">
                        @if ($currentConference->hasMeta('description'))
                            <div class="cf-description prose prose-sm -mt-2 text-secondary">
                                <p>{!! $currentConference->getMeta('description') !!}</p>
                                <div class="flex flex-wrap w-full gap-2 mt-4 md:max-w-[20rem]">
                                    @foreach ($topics as $topic)
                                        <span class="badge badge-outline badge-md text-xs text-secondary">{{ $topic->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="body-font text-gray-600 ms-3">
                <div class="max-w-7xl container mx-auto py-4">
                    <div class="mb-4 flex w-full flex-wrap ps-5">
                        <p class="prose prose-lg text-secondary">Upcoming Conferences</p>
                    </div>

                    <div class="cf flex flex-wrap mx-auto -mt-5" x-masonry=".cf-upcoming">
                        @foreach ($upcomings as $upcoming)
                            <div
                                class="cf-upcoming w-full sm:max-w-[200px] md:max-w-full lg:max-w-[320px] xl:max-w-[300px] p-4">
                                <div class="rounded border bg-white p-6">

                                    @if ($upcoming->hasMedia('thumbnail'))
                                        <img class="xs:h-72 mb-6 h-72 w-full rounded object-cover object-left-top sm:h-72 md:h-64 lg:h-60 xl:h-56"
                                            src="{{ $upcoming->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                            alt="{{ $upcoming->name }}" />
                                    @endif

                                    @if ($upcoming->hasMeta('date_held'))
                                        <div class="inline-flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                            </svg>

                                            <p class="text-xs text-secondary">Start on
                                                {{ $upcoming->getMeta('date_held') }}</p>
                                        </div>
                                    @endif

                                    <h2 class="title-font mb-3 mt-1 text-lg prose prose:lg text-secondary">{{ $upcoming->name }}
                                    </h2>
                                    @if ($upcoming->hasMeta('description'))
                                        <p class="prose prose-sm text-secondary">{!! Str::words($upcoming->getMeta('description'), 15) !!}</p>
                                    @endif
                                    <div class="flex justify-end mt-2">
                                        <button
                                            class="btn btn-primary btn-sm btn-outline rounded mt-3 font-normal">Enrol</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

</x-conference::layouts.main>
