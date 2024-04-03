<x-website::layouts.main>
    <div class="space-y-5">
        <div class="description user-content">
            {{ new Illuminate\Support\HtmlString($site->getMeta('about')) }}
        </div>
        <div class="sponsors space-y-4" x-data="carousel">
            <h2 class="text-xl font-medium">Our Partners</h2>
            <div class="sponsors-carousel flex items-center w-full gap-4" x-bind="carousel">
                <button x-on:click="toLeft"
                    class="bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full flex items-center justify-center">
                    <x-heroicon-m-chevron-left class="h-6 w-fit text-white" />
                </button>
                <ul x-ref="slider" class="flex-1 flex w-full snap-x snap-mandatory overflow-x-scroll gap-3 pb-4">
                    @foreach ($sponsors as $sponsor)
                        <li @class([
                            'flex w-1/6 shrink-0 snap-start flex-col items-center justify-center',
                            'ml-auto' => $loop->first,
                            'mr-auto' => $loop->last,
                        ])>
                            <img class="w-full" src="{{ $sponsor->getFirstMediaUrl('logo') }}"
                                alt="{{ $sponsor->name }}">
                        </li>
                    @endforeach
                </ul>
                <button x-on:click="toRight"
                    class="bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full flex items-center justify-center">
                    <x-heroicon-m-chevron-right class="h-6 w-fit text-white" />
                </button>
            </div>
        </div>
    </div>
</x-website::layouts.main>
