    <div class="block space-y-2">
        @if (count($userContent) > 0)
            <h2 class="ml-1 text-heading">Menu</h2>
            <div class="card card-compact bg-white border">
                <div class="card-body">
                    <div class="max-w-2xl">
                        @foreach ($userContent as $content)
                            <div class="bg-white" x-data="{ accordOpen: false }">
                                <div class="accordion-header" @click="accordOpen = !accordOpen">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary"
                                        :class="{ 'rotate-90': accordOpen, '-translate-y-0.0': !accordOpen }">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                    <a href="{{ route('livewirePageGroup.current-conference.pages.static-page', ['staticPage' => $content->slug]) }}"
                                        class="text-xs text-primary hover:text-blue-500">{{ $content->title }}</a>
                                </div>

                                <div class="accordion-body" x-cloak x-show="accordOpen" x-collapse
                                    x-collapse.duration.400ms>
                                    <div class="px-2 py-1">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
