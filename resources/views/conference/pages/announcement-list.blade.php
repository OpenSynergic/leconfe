<x-conference::layouts.main>
        <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
            <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-l-lg" src="/docs/images/blog/image-4.jpg" alt="">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy technology acquisitions 2021</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
            </div>
        </a>
        <div class="card-body">
            <h1 class="text-2xl">{{ $contentTitle }}</h1>
            @foreach ($staticPageList as $staticPage)    
                <div class="block w-full px-3 py-2 bg-white border border-gray-300 rounded-md">
                    <h5 class="text-xl">{{ $staticPage->title }}</h5>
                    <div class="card-actions justify-end mb-2">
                        <a href="{{ route('livewirePageGroup.current-conference.pages.announcement-page', ['content_type' => $contentTypeSlug, 'user_content' => $staticPage->id]) }}" class="btn btn-primary font-normal btn-sm">Read more</a>
                    </div>
                </div>
            @endforeach
        </div>
</x-conference::layouts.main>