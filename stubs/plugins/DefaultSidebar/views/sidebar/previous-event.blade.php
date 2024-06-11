@if ($previousEvents->isNotEmpty())
    <x-website::sidebar class="sidebar-previous-event space-y-1" :id="$id">
        <h2 class="text-heading">Previous Event</h2>
        @foreach ($previousEvents as $event)
            <div class="card card-compact bg-white border">
                <div class="card-body">
                    <a href="{{ $event->getHomeUrl() }}" class="flex items-center">
                        <h2 class="ml-1 text-base text-primary">{{ $event->title }}</h2>
                    </a>
                </div>
            </div>
        @endforeach
    </x-website::sidebar>
@endif
