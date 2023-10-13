<x-website::layouts.main>
<section class="h-screen">
        <div class="flex flex-wrap py-10">
            <div class="flex flex-col ps-6 basis-1/2">
                <div class="flex flex-col space-y-1 mb-3">
                    <h2 class="text-heading">Event</h2>
                    <div class="border border-primary w-12"></div>
                </div>

                <ol class="border-l border-neutral-300 dark:border-neutral-500 flex flex-col gap-y-6">
                    @forelse ($events as $event)
                        <li class="flex flex-col gap-y-2">
                            <div class="flex-start flex items-center pt-3">
                                <div
                                    class="-ml-[5px] mr-3 h-[9px] w-[9px] rounded-full bg-neutral-300 dark:bg-neutral-500">
                                </div>
                                <p class="text-sm font-semibold">{{ $event->title }}</p>
                            </div>
                            <p class="text-sm text-gray-500 ps-4">
                                {{ date(setting('format.date'), strtotime($event->date)) }}
                            </p>
                            <time class="text-xs ps-4 text-gray-500">{{ $event->subtitle }}</time>

                            <div class="ps-4 inline-flex gap-2 flex-wrap">
                                @foreach ($event->roles as $role)
                                    @php
                                        $badgeRole = '';
                                        $badgeRole = match ($role) {
                                            'Author' => 'author-badge',
                                            'Editor' => 'editor-badge',
                                            'Reviewer' => 'reviewer-badge',
                                            'Participant' => 'participant-badge',
                                            default => 'participant-badge',
                                        };
                                    @endphp
                                    <div class="{{ $badgeRole }}">{{ $role }}</div>
                                @endforeach
                            </div>
                        </li>
                    @empty
                    @endforelse
                </ol>
            </div>
        </div>

    </section>
</x-website::layouts.main>
