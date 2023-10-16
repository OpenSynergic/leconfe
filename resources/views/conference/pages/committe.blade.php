<div class="mx-auto max-w-7xl w-full bg-white">
    <section class="px-6 lg:px-24 flex flex-col py-20 gap-y-6">
        <h1 class="text-2xl font-bold text-start">Commitee List</h1>
        <div class="flex flex-col items-start gap-y-6">
            <div class="flex w-full gap-x-10 flex-wrap  ms-1 gap-y-10 items-start">
                @forelse ($groupedCommittes as $positionName => $members)
                    <div class="flex flex-col w-full space-y-6 md:w-[320px]">
                        <h2 class="text-xl">{{ $positionName }}</h2>
                        @forelse ($members as $member)
                            <div class="w-full border px-4 py-4">
                                <div class="inline-flex items-start gap-x-2 mb-1">
                                    @if ($member->hasMedia('profile'))
                                        <div class="avatar">
                                            <div class="w-14 rounded-full">
                                                <img src="{{ $member->getFirstMediaUrl('profile') }}" />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex flex-col gap-2">
                                        <p class="text-sm">{{ $member->fullName }}</p>
                                        <div class="flex flex-col text-xs space-y-3">
                                            @if ($member->hasMeta('affiliation'))
                                                <div class="inline-flex items-center gap-2">
                                                    <x-forkawesome-university class="w-4 h-4" />
                                                    <p>{{ $member->getMeta('affiliation') }}</p>
                                                </div>
                                            @endif
                                            <div class="inline-flex items-center gap-2">
                                                <x-heroicon-m-envelope class="w-4 h-4" />
                                                <p>{{ $member->email ?? '' }}</p>
                                            </div>
                                            <div class="inline-flex gap-x-2 gap-y-3 flex-wrap w-full">
                                                <x-heroicon-s-trophy class="w-4 h-4" />
                                                @forelse ($member->getMeta('expertise') as $expertise)
                                                    <span class="badge badge-xs text-mini">{{ $expertise }}</span>
                                                @empty
                                                @endforelse
                                            </div>

                                            <div class="inline-flex gap-x-1">
                                                @if ($member->hasMeta('orcid_id'))
                                                    <a href="https://orcid.org/{{ $member->getMeta('orcid_id') }}">
                                                        <x-academicon-orcid class="w-5 h-5 text-[#A9D03F]" /></a>
                                                @endif

                                                @if ($member->hasMeta('google_scholar_id'))
                                                    <a
                                                        href="https://scholar.google.com/citations?user={{ $member->getMeta('google_scholar_id') }}">
                                                        <x-academicon-google-scholar class="w-5 h-5 text-[#4889F4]" />
                                                    </a>
                                                @endif

                                                @if ($member->hasMeta('scopus_id'))
                                                    <a
                                                        href="https://www.scopus.com/authid/detail.uri?authorId={{ $member->getMeta('scopus_id') }}">
                                                        <x-academicon-scopus
                                                            class="w-5 h-5 text-[#FF8608] stroke-[#FF8608]" /> </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

                @empty

                @endforelse
            </div>
        </div>

    </section>
</div>
