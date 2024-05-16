<div>
    @php
        $authors = [
            ['name' => 'John Doe', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Jane Doe', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Juan Dela Cruz', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Maria Dela Cruz', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Pedro Dela Cruz', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Juan Tamad', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Maria Tamad', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Pedro Tamad', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Juan Dela Cruz', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Maria Dela Cruz', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
            ['name' => 'Pedro Dela Cruz', 'url' => 'https://www.arcc-journal.org/index.php/arccjournal'],
        ];
    @endphp
    <div class="flex mb-5 space-x-4">
        <div class="text-xl font-semibold min-w-fit">Articles</div>
        <hr class="w-full h-px my-auto bg-gray-200 border-0 dark:bg-gray-700">
    </div>
    <div class="space-y-5">
        <div class="grid grid-cols-10 space-x-5">
            <div class="col-span-2 max-h-36">
                <img class="w-full h-full" src="https://t4.ftcdn.net/jpg/05/52/69/73/360_F_552697386_oo8dP8v1YrBES6xpWy9JIGZkDLDXHZGj.jpg" alt="">
            </div>
            <div class="col-span-8">
                <div class="mb-3 text-base">
                    <a href="#" class="font-semibold text-gray-700 hover:text-primary">The Impact of COVID-19 on the Economy</a>
                    <div class="text-xs text-gray-500">Jama Network Article</div>
                </div>
                <div class="grid grid-cols-9 mb-3">
                    <div class="w-full col-span-8 text-xs">
                        @foreach ($authors as $author)
                            <a href="{{ $author['url'] }}" class="text-gray-500 hover:text-primary">{{ $author['name'] }}</a>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>
                    <div class="col-span-1 text-xs text-right">
                        <div class="flex justify-end gap-x-1">
                            <x-heroicon-o-document-text class="w-4 h-4 my-auto text-gray-500" />
                            <span>15-20</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end text-xs">
                    {{-- <div class="flex space-x-6 text-gray-700">
                        <div class="flex space-x-1">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4 my-auto text-gray-500" />
                            <span>Abstract Views : 123</span>
                        </div>
                        <div class="flex space-x-1">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 my-auto text-gray-500" />
                            <span>Download : 123</span>
                        </div>
                    </div> --}}
                    <a href="#" class="flex space-x-1 text-gray-500 hover:text-primary">
                        <x-academicon-doi class="w-4 h-4 my-auto text-yellow-400" />
                        <span>DOI : 10.20473/ijosh.v13i1.2024.13-19</span>
                    </a>
                </div>
                <div class="flex space-x-1.5">
                    <button class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8">
                        PDF
                    </button>
                    <button class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8">
                        XML
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-10 space-x-5">
            <div class="col-span-2 max-h-36">
                <img class="w-full h-full" src="https://www.shutterstock.com/image-photo/there-notebook-word-coo-abbreviation-600nw-2366080151.jpg" alt="">
            </div>
            <div class="col-span-8">
                <div class="mb-3 text-base">
                    <a href="#" class="font-semibold text-gray-700 hover:text-primary">Econometric Models for Industrial Organization</a>
                    <div class="text-xs text-gray-500">Jama Network Article</div>
                </div>
                <div class="grid grid-cols-9 mb-3">
                    <div class="w-full col-span-8 text-xs">
                        @foreach ($authors as $author)
                            <a href="{{ $author['url'] }}" class="text-gray-500 hover:text-primary">{{ $author['name'] }}</a>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>
                    <div class="col-span-1 text-xs text-right">
                        <div class="flex justify-end gap-x-1">
                            <x-heroicon-o-document-text class="w-4 h-4 my-auto text-gray-500" />
                            <span>32-44</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end text-xs">
                    {{-- <div class="flex space-x-6 text-gray-700">
                        <div class="flex space-x-1">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4 my-auto text-gray-500" />
                            <span>Abstract Views : 123</span>
                        </div>
                        <div class="flex space-x-1">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 my-auto text-gray-500" />
                            <span>Download : 123</span>
                        </div>
                    </div> --}}
                    <a href="#" class="flex space-x-1 text-gray-500 hover:text-primary">
                        <x-academicon-doi class="w-4 h-4 my-auto text-yellow-400" />
                        <span>DOI : 10.20473/ijosh.v13i1.2024.13-19</span>
                    </a>
                </div>
                <div class="flex space-x-1.5">
                    <button class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8">
                        PDF
                    </button>
                    <button class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8">
                        XML
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-10 space-x-5">
            <div class="col-span-2 max-h-36">
                <img class="w-full h-full" src="https://icsblog.s3.ap-south-1.amazonaws.com/blog/wp-content/uploads/2022/09/20151636/Pros-and-Cons-of-Studying-Economics.jpg" alt="">
            </div>
            <div class="col-span-8">
                <div class="mb-3 text-base">
                    <a href="#" class="font-semibold text-gray-700 hover:text-primary">Financial Mathematics for Actuaries</a>
                    <div class="text-xs text-gray-500">Jama Network Article</div>
                </div>
                <div class="grid grid-cols-9 mb-3">
                    <div class="w-full col-span-8 text-xs">
                        @foreach ($authors as $author)
                            <a href="{{ $author['url'] }}" class="text-gray-500 hover:text-primary">{{ $author['name'] }}</a>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>
                    <div class="col-span-1 text-xs text-right">
                        <div class="flex justify-end gap-x-1">
                            <x-heroicon-o-document-text class="w-4 h-4 my-auto text-gray-500" />
                            <span>9-10</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end text-xs">
                    {{-- <div class="flex space-x-6 text-gray-700">
                        <div class="flex space-x-1">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4 my-auto text-gray-500" />
                            <span>Abstract Views : 123</span>
                        </div>
                        <div class="flex space-x-1">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 my-auto text-gray-500" />
                            <span>Download : 123</span>
                        </div>
                    </div> --}}
                    <a href="#" class="flex space-x-1 text-gray-500 hover:text-primary">
                        <x-academicon-doi class="w-4 h-4 my-auto text-yellow-400" />
                        <span>DOI : 10.20473/ijosh.v13i1.2024.13-19</span>
                    </a>
                </div>
                <div class="flex space-x-1.5">
                    <button class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8">
                        PDF
                    </button>
                    <button class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8">
                        XML
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>