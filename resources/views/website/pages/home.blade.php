<x-conference::layouts.main>
    <div class="card-body space-y-2">
        <div class="conference-highlight space-y-4">
            <h2 class="card-title">Highlight Conference</h2>
            <div class="flex flex-col sm:flex-row gap-4">
                @if($currentConference->hasMedia('thumbnail'))
                <div class="cf-thumbnail sm:max-w-[12rem]">
                    <img class="w-full" src="{{ $currentConference->getFirstMediaUrl('thumbnail', 'thumb') }}"
                        alt="{{ $currentConference->name }}">
                </div>
                @endif
                <div class="cf-information space-y-2 w-full">
                    <div class="flex flex-wrap justify-between items-center">
                        <h3 class="text-lg">{{ $currentConference->name }}</h3>
                        <div>
                            <div class="badge badge-info badge-sm  uppercase">{{ $currentConference->type }}</div>
                        </div>
                    </div>
                    <div class="cf-description prose prose-sm max-w-none">
                        @if($currentConference->getMeta('location') && $currentConference->getMeta('date_held'))
                        <p>{{ $currentConference->getMeta('location') }}, {{ $currentConference->getMeta('date_held') }}</p>
                        @endif
                        {!! $currentConference->getMeta('description') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-conference::layouts.main>
