@if ($committees->isNotEmpty())
    <x-website::sidebar class="sidebar-committees space-y-1" :id="$id">
        <h2 class="text-heading">Committees</h2>
        @foreach ($committees as $committee)
            <div class="bg-white border card card-compact">
                <div class="gap-2 card-body">
                    <div class="flex flex-col gap-4">
                        <div class="flex gap-x-2">
                            @if ($committee->hasMedia('profile'))
                                <div class="profile-image avatar">
                                    <div class="w-12 h-12 rounded-full">
                                        <img src="{{ $committee->getFirstMedia('profile')->getAvailableUrl(['avatar', 'thumb', 'thumb-xl']) }}"
                                            alt="{{ $committee->fullName }}" />
                                    </div>
                                </div>
                            @endif
                            <div class="profile-description">
                                <p class="text-content">{{ $committee->fullName }}</p>
                                @if ($committee->hasMeta('affiliation'))
                                    <span class="text-xs">{{ $committee->getMeta('affiliation') }}</span>
                                @endif
                            </div>
                        </div>
                        @if ($committee->hasMeta('expertise'))
                            <div class="inline-flex flex-wrap gap-2">
                                @foreach ($committee->getMeta('expertise') as $expertise)
                                    <div class="h-6 text-xs border border-gray-300 badge badge-outline">
                                        {{ $expertise }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <div class="flex justify-end w-full pt-1">
            <a href="{{ route('livewirePageGroup.conference.pages.committees') }}" class="btn btn-primary btn-sm">
                More
            </a>
        </div>
    </x-website::sidebar>
@endif
