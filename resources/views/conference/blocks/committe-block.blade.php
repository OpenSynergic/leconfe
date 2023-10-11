<div class="block space-y-1">

        @if (count($participants) > 0)
        <h2 class="ml-1 text-heading mb-1">Committee</h2>
        @foreach ($participants as $participant)
                    <!-- Loop through each member of the current position -->
                    <div class="card card-compact bg-white border">
                        <div class="card-body gap-2">
                            <!-- Code for displaying member information -->
                            <div class="flex flex-col gap-4">
                                <!-- Display member information -->
                                <div class="flex gap-x-2">
                                    @if ($participant->hasMedia('profile'))
                                        <!-- Check if the member has a profile image -->
                                        <div class="profile-image avatar">
                                            <div class="w-10 rounded-full">
                                                <img src="{{ $participant->getFirstMediaUrl('profile') }}"
                                                    alt="{{ $participant->fullName }}" />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="profile-description">
                                        <p class="text-content">{{ $participant->fullName }}</p>
                                        <!-- Display member's full name -->
                                        @if ($participant->hasMeta('affiliation'))
                                            <!-- Check if the member has an affiliation -->
                                            <span class="small-text">{{ $participant->getMeta('affiliation') }}</span>
                                            <!-- Display member's affiliation -->
                                        @endif
                                    </div>
                                </div>
                                @if ($participant->hasMeta('expertise'))
                                    <!-- Check if the member has expertise information -->
                                    <div class="inline-flex flex-wrap gap-2">
                                        @foreach ($participant->getMeta('expertise') as $expertise)
                                            <!-- Loop through and display member's expertise badges -->
                                            <div class="badge badge-outline text-xs border border-gray-300 h-6 small-text">
                                                {{ $expertise }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="w-full flex justify-start pt-1">
                    <a href="{{ route('livewirePageGroup.current-conference.pages.committe') }}" class="btn btn-primary text-xs btn-sm text-white rounded-md" id="showMoreButton">More</a>
                </div>
        @endif

</div>
