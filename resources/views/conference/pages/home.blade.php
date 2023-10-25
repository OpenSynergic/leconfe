<x-website::layouts.main>
    <div class="flex flex-col gap-2 mt-10">
        <section id="current-conference">
            <h2 class="text-heading mb-2 ms-5">Current Conference</h2>
            <div class="card px-5 py-3 -mt-2">
                <div class="card-body space-y-2 border rounded">
                    <div class="py-4 px-2 -mt-1">
                        <h2 class="text-heading -mt-2">{{ $currentConference->name }}</h2>
                        @if ($currentConference->hasMeta('date_held'))
                            <div class="inline-flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                </svg>
                                <time
                                    class="text-xs text-secondary">{{ date(setting('format.date'), strtotime($currentConference->getMeta('date_held'))) }}</time>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row space-x-4">
                            @if ($currentConference->hasMedia('thumbnail'))
                                <div class="cf-thumbnail sm:max-w-[10rem]">
                                    <img class="h-full w-full rounded object-cover aspect-[4/3]"
                                        src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                        alt="{{ $currentConference->name }}" />
                                </div>
                            @endif
                            <div class="flex flex-col gap-2 mt-3">
                                @if ($currentConference->hasMeta('description'))
                                    <div class="prose text-justify">
                                        <p class="text-content -mt-2">{{ $currentConference->getMeta('description') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="conference-information">
            <div class="card-body space-y-2 -mt-2">
                <div class="cf-information">
                    <h2 class="text-heading ms-1 pb-1">Information</h2>
                    @if ($currentConference->hasMeta('date_held') || $currentConference->hasMeta('location'))
                        <table class="w-full" cellpadding="4">
                            <tr>
                                <td width="80">Type</td>
                                <td width="20">:</td>
                                <td>{{ $currentConference->type }}</td>
                            </tr>
                            <tr>
                                <td>Place</td>
                                <td>:</td>
                                <td>{{ $currentConference->getMeta('location') }}</td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>:</td>
                                <td>{{ date(setting('format.date'), strtotime($currentConference->getMeta('date_held'))) }}</td>
                            </tr>
                        </table>
                    @else
                        <div class="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="h-6 w-6 shrink-0 stroke-info">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Data Not Available.</span>
                        </div>
                    @endif

                </div>
            </div>
        </section>

        <section id="conference-speakers">
            <h2 class="text-heading mb-2 ms-5">Speakers</h2>
            <div class="card px-5">
                @foreach ($participantPosition as $position)
                    <div class=" space-y-4 mb-6">
                        <h3 class="text-content">{{ $position->name }}</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($position->participants as $participant)
                                <div class="flex items-center space-x-2">
                                    <div class="avatar">

                                        <div
                                            class="h-14 w-14 rounded-full ring ring-2 ring-primary ring-offset-2 sm:w-16 sm:h-16">
                                            <img src=" {{ $participant->getFirstMediaUrl('profile') }} "
                                                alt="" />
                                        </div>

                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-xs text-secondary">{{ $participant->given_name }}
                                            {{ $participant->family_name }}</p>

                                        <div>
                                            @foreach ($participant->getMeta('expertise') ?? [] as $expertise)
                                                <small class="text-2xs text-primary">{{ $expertise }}</small>
                                                @if ($loop->iteration >= 2)
                                                    @break
                                                @endif
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </div>
                                        <small
                                            class="text-2xs text-secondary">{{ $participant->getMeta('affiliation') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

            {{-- additional content start --}}
        <section class="user-content px-5">
            {!! $currentConference->getMeta('additional_content') !!}
        </section>
            {{-- addtional content end --}}
    </div>
</x-website::layouts.main>
