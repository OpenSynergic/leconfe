<div class="w-full mx-auto bg-white max-w-7xl">
    <section class="flex flex-col px-6 py-20 lg:px-24 gap-y-6">
        <h1 class="text-2xl font-bold text-start">Commitee List</h1>
        <div class="flex flex-col items-start gap-y-6">
            <div class="flex flex-wrap items-start w-full gap-x-10 ms-1 gap-y-10">
                @forelse ($groupedCommittes as $key => $members)
                    <div class="flex flex-col w-full space-y-6 md:w-[320px]">
                        <h2 class="text-xl">{{ $groupedCommittes->name }}</h2>
                        @forelse ($members->committees as $member)
                            <div class="w-full px-4 py-4 border">
                                <div class="inline-flex items-start mb-1 gap-x-2">
                                    @if ($member->hasMedia('profile'))
                                        <div class="avatar">
                                            <div class="rounded-full w-14">
                                                <img src="{{ $member->getFirstMediaUrl('profile') }}" />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex flex-col gap-2">
                                        <p class="text-sm">{{ $member->fullName }}</p>
                                        <div class="flex flex-col space-y-3 text-xs">
                                            @if ($member->hasMeta('affiliation'))
                                                <div class="inline-flex items-center gap-2">
                                                    <x-lineawesome-university-solid class="w-4 h-4" />
                                                    <p>{{ $member->getMeta('affiliation') }}</p>
                                                </div>
                                            @endif
                                            <div class="inline-flex items-center gap-2">
                                                <x-heroicon-m-envelope class="w-4 h-4" />
                                                <p>{{ $member->email ?? '' }}</p>
                                            </div>
                                            <div class="flex flex-wrap items-center w-full gap-x-2 gap-y-3">
                                                <x-heroicon-s-trophy class="w-4 h-4" />
                                                @foreach ($member->getMeta('expertise') as $expertise)
                                                    <span class="badge badge-xs text-mini">{{ $expertise }}</span>
                                                @endforeach
                                            </div>
                                            @if($member->getMeta('orcid_id') || $member->getMeta('google_scholar_id') || $member->getMeta('scopus_id'))
                                            <div class="inline-flex gap-x-1">
                                                @if ($member->getMeta('orcid_id'))
                                                    <a href="https://orcid.org/{{ $member->getMeta('orcid_id') }}">
                                                        <x-academicon-orcid class="w-5 h-5 text-[#A9D03F]" /></a>
                                                @endif

                                                @if ($member->getMeta('google_scholar_id'))
                                                    <a
                                                        href="https://scholar.google.com/citations?user={{ $member->getMeta('google_scholar_id') }}">
                                                        <x-academicon-google-scholar class="w-5 h-5 text-[#4889F4]" />
                                                    </a>
                                                @endif

                                                @if ($member->getMeta('scopus_id'))
                                                    <a
                                                        href="https://www.scopus.com/authid/detail.uri?authorId={{ $member->getMeta('scopus_id') }}">
                                                        <x-academicon-scopus
                                                            class="w-5 h-5 text-[#FF8608] stroke-[#FF8608]" /> </a>
                                                @endif
                                            </div>
                                            @endif
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
