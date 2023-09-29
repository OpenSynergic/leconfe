<div class="flex flex-col space-y-1">
    @if (count($timelines) > 0)
        <h2 class="text-heading px-2 mb-1">Informations</h2>
        @foreach ($timelines as $timeline)
            <div class="card card-compact bg-white border w-full p-3 flex-col rounded">
                <div class="w-full flex justify-between">
                    <div class="inline-flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                        <time class="small-text">{{ date('d M Y', strtotime($timeline->date)) }}</time>
                    </div>
                <div class="flex flex-wrap gap-1">
                    @if (count($timeline->roles) > 0)
                    @foreach ($timeline->roles as $role)
                        @php
                            $badgeRole = '';
                            switch ($role) {
                                case 'Author':
                                    $badgeRole = 'author-badge';
                                    break;
                                case 'Editor':
                                    $badgeRole = 'editor-badge';
                                break;
                                case 'Reviewer':
                                    $badgeRole = 'reviewer-badge';
                                    break;
                                case 'Participant':
                                    $badgeRole = 'participant-badge';
                                    break;
                            }
                        @endphp
                          <div class="inline-flex">
                            <span class="{{ $badgeRole }}">
                                {{ $role }}
                            </span>
                          </div>
                    @endforeach
                @endif
                </div>
                </div>
                <div class="flex flex-col gap-2 mt-2">
                    <h5 class="text-subheading">{{ $timeline->title }}</h5>
                    <span class="small-text -mt-1">{{ $timeline->subtitle ?? ''}}</span>
                </div>
            </div>
        @endforeach
    @endif
</div>
