@use('App\Models\Enums\SubmissionStatus')
@use('App\Constants\SubmissionFileCategory')
<x-website::layouts.main>
    <div class="p-5 submission-list">
        {{-- @foreach ($topics as $topic)
            <div @class(['font-semibold text-2xl', 'mt-7' => !$loop->first])>
                {{ $topic->name }}
            </div>
            @forelse($submissions = $topic->submissions()->published()->get() as $submission)
                <div class="pb-4 mt-2 border-b border-b-slate-100">
                    <a href="{{ route('livewirePageGroup.conference.pages.submission-detail', $submission->getKey()) }}"
                        class="text-xl link-primary">
                        {{ $submission->getMeta('title') }}
                    </a>

                    <div class="flex items-center text-sm text-slate-700">
                        <x-lineawesome-user class="w-4 h-4 mr-0.5" />
                        {{ $submission->contributors()->with('contributor')->get()->pluck('contributor.fullName')->join(', ') }}
                    </div>
                    <div class="flex items-center text-sm text-slate-700">
                        <x-lineawesome-calendar-check-solid class="w-4 h-4 mr-0.5" />
                        {{ __('Date Published') . ': ' . $submission->published_at->format(Settings::get('format_date')) }}
                    </div>
                    @if (($files = $submission->getMedia(SubmissionFileCategory::EDITED_FILES)) && $files->count())
                        <div class="flex flex-wrap gap-1.5 text-sm mt-2">
                            @foreach ($files as $file)
                                <a href="{{ route('submission-files.view', $file->uuid) }}" target="_blank"
                                    class="flex items-center px-3 py-1 break-all transition duration-200 ease-in-out border rounded-md shadow-sm bg-slate-100 border-slate-200 link-primary hover:bg-slate-200 hover:border-slate-300">
                                    {{ $file->file_name }}
                                    <x-lineawesome-external-link-square-alt-solid class="w-4 h-4 ml-0.5" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-400 ">
                    No Submission
                </div>
            @endforelse
        @endforeach --}}
        <div class="mb-6 bg-gray-50 dark:bg-gray-800">
            <div class="container flex items-center px-6 py-4 mx-auto overflow-x-auto text-sm whitespace-nowrap">
                <a href="#" class="text-gray-600 dark:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                </a>
        
                <span class="mx-5 text-gray-500 dark:text-gray-300 rtl:-scale-x-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
        
                <a href="#" class="text-gray-600 dark:text-gray-200 hover:underline">
                    Proceedings
                </a>
        
                <span class="mx-5 text-gray-500 dark:text-gray-300 rtl:-scale-x-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
        
                <a href="#" class="text-gray-600 dark:text-gray-200 hover:underline">
                    Vol. 10 No. 2 (2022): December
                </a>
            </div>
        </div>
        <div class="mb-12">
            <div class="flex mb-5 space-x-4">
                <div class="text-xl font-semibold min-w-fit">Current Proceeding</div>
                <hr class="w-full h-px my-auto bg-gray-200 border-0 dark:bg-gray-700">
            </div>
            <div class="grid grid-cols-8 gap-x-5">
                <div class="col-span-2 max-w-64">
                    <img src="https://mf-journal.com/public/site/images/admin/modern%20finance%20cover.webp" class="w-full" alt="150">
                </div>
                <div class="col-span-6">
                    <div class="space-y-4">
                        <div class="text-sm font-semibold">
                            Vol. 10 No. 2 (2022): December
                        </div>
                        <div class="text-sm text-justify">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio, 
                            eius ipsam at iste nemo odio. Cum obcaecati hic, rerum aliquid soluta nobis voluptatum, 
                            deserunt deleniti corrupti, commodi quasi fugiat dolor! Lorem ipsum dolor sit amet, 
                            consectetur adipisicing elit. Maiores unde at, perferendis vero suscipit nam fuga facilis hic dolores aut ut, 
                            nulla dolore quis ex? Natus consectetur laboriosam deleniti quod!
                        </div>
                        <div class="text-sm">
                            <span class="font-semibold">Published: </span> 2024-01-01
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-conference::list-articles />
    </div>
</x-website::layouts.main>
