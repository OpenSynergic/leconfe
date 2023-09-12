<x-conference::layouts.main>

    <div class="px-6 py-4">
        <h1 class="card-title font-normal font-bold mt-4">Highlight Conference</h1>
        <div class="h-1 w-20 rounded bg-primary"></div>
    </div>

    <div class="card px-5 py-4">
        <div class="card-body space-y-2 bg-gray-50 border">
            <div class="cf-current space-y-1 p-4">
                <div class="flex justify-between">
                    <div class="card-title font-normal">{{ $currentConference->name }}</div>
                </div>

                @if ($currentConference->hasMeta('date_held'))
                    <div class="pb-2">
                        <div class="inline-flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                            <span class="font-normal text-xs">{{ $currentConference->getMeta('date_held') }}</span>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col gap-2 sm:flex-row">
                    @if ($currentConference->hasMedia('thumbnail'))
                        <div class="cf-thumbnail w-[1240px] sm:max-w-[12rem]">
                            <img class="h-full w-full rounded object-cover sm:object-left-top"
                                src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}" alt="" />
                        </div>
                    @endif
                    <div class="flex flex-col gap-2">
                        @if ($currentConference->hasMeta('description'))
                            <div class="cf-description h-full w-full">
                                <p class="text-[.65rem]">{!! $currentConference->getMeta('description') !!}</p>
                                <div class="flex flex-wrap w-full gap-2 mt-4 md:max-w-[30rem]">
                                    @foreach ($topics as $topic)
                                        <span class="badge badge-outline text-xs">{{ $topic->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <section class="body-font text-gray-600">
                <div class="max-w-7x1 container mx-auto py-5">
                    <div class="mb-4 flex w-full flex-wrap p-4">
                        <div class="mb-6 w-full lg:mb-0">
                            <h1 class="card-title font-normal font-bold">Upcoming Conferences</h1>
                        </div>
                    </div>

                    <div class="cf flex flex-wrap mx-auto">
                        @foreach ($upcomings as $upcoming)
                            <div class="cf-upcoming w-full sm:max-w-[200px] md:max-w-full lg:max-w-[320px] xl:max-w-[300px] p-4"">
                                <div class="rounded-lg border bg-white p-6">

                                    @if ($upcoming->hasMedia('thumbnail'))
                                        <img class="xs:h-72 mb-6 h-72 w-full rounded object-cover object-left-top sm:h-72 md:h-64 lg:h-60 xl:h-56"
                                            src="{{ $upcoming->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                            alt="{{ $upcoming->name }}" />
                                    @endif

                                    @if ($upcoming->hasMeta('date_held'))
                                       <div class="inline-flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                          </svg>

                                        <h3 class="title-font text-gray-600 text-xs font-medium tracking-widest">Start on
                                            {{ $upcoming->getMeta('date_held') }}</h3>
                                       </div>
                                    @endif

                                    <h2 class="title-font mb-4 text-lg font-medium text-gray-900">{{ $upcoming->name }}
                                    </h2>
                                    @if ($upcoming->hasMeta('description'))
                                        <p class="text-sm leading-relaxed">{!! Str::words($upcoming->getMeta('description'), 15) !!}</p>
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
        </div>
    </div>

</x-conference::layouts.main>
