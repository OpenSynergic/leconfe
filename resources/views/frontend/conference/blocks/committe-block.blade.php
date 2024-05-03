<x-block :id="$id" class="space-y-1">
    @if (count($committees) > 0)
        <h2 class="mb-1 ml-1 text-heading">Committee</h2>
        @foreach ($committees as $committee)
            <!-- Loop through each member of the current position -->
            <div class="bg-white border card card-compact">
                <div class="gap-2 card-body">
                    <!-- Code for displaying member information -->
                    <div class="flex flex-col gap-4">
                        <!-- Display member information -->
                        <div class="flex gap-x-2">
                            @if ($committee->hasMedia('profile'))
                                <!-- Check if the member has a profile image -->
                                <div class="profile-image avatar">
                                    <div class="w-10 rounded-full">
                                        <img src="{{ $committee->getFirstMediaUrl('profile') }}"
                                            alt="{{ $committee->fullName }}" />
                                    </div>
                                </div>
                            @endif
                            <div class="profile-description">
                                <p class="text-content">{{ $committee->fullName }}</p>
                                <!-- Display member's full name -->
                                @if ($committee->hasMeta('affiliation'))
                                    <!-- Check if the member has an affiliation -->
                                    <span
                                        class="text-xs text-secondary">{{ $committee->getMeta('affiliation') }}</span>
                                    <!-- Display member's affiliation -->
                                @endif
                            </div>
                        </div>
                        @if ($committee->hasMeta('expertise'))
                            <!-- Check if the member has expertise information -->
                            <div class="inline-flex flex-wrap gap-2">
                                @foreach ($committee->getMeta('expertise') as $expertise)
                                    <!-- Loop through and display member's expertise badges -->
                                    <div
                                        class="h-6 text-xs border border-gray-300 badge badge-outline text-secondary">
                                        {{ $expertise }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <div class="flex justify-end w-full pt-1">
            <a href="{{ route('livewirePageGroup.conference.pages.committe') }}"
                class="text-xs text-white rounded-md btn btn-primary btn-sm" id="showMoreButton">More</a>
        </div>
    @endif

</x-block>
