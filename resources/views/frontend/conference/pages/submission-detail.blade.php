@use('App\Constants\SubmissionFileCategory')
<x-website::layouts.main>
    <div class="p-5" id="submission-detail">
        <h1 class="text-2xl">
            {{ $submission->getMeta('title') }}
        </h1>
        <div class="text-sm text-slate-400 mb-4">
            <span class="flex items-center">
                <x-lineawesome-calendar-check-solid class="w-3 h-3 mr-0.5" />
                {{ __('Date Published') . ': ' . $submission->published_at->format(Settings::get('date')) }}
            </span>
        </div>
        <div class="submission-detail space-y-7">
            <section class="contributors">
                <h2 class="text-xl font-medium pb-1 mb-3 border-b border-b-slate-200">
                    {{ __('Contributors') }}
                </h2>
                {{-- Contributors --}}
                <div
                    class="mt-3 p-5 bg-slate-100 border border-slate-200 shadow-sm rounded-md grid grid-cols-2 gap-4 text-slate-700">
                    @foreach ($submission->contributors()->with(['participant', 'position'])->get() as $contributor)
                        <div class="col-span-1">
                            <div class="flex items-center">
                                <x-lineawesome-user class="w-5 h-5 mr-1" />
                                {{ $contributor->participant->fullName }}
                            </div>
                            <span class="ml-[25px] text-sm text-slate-500">{{ $contributor->position->name }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
            {{-- Keywords --}}
            <section class="keywords">
                <div class="text-slate-800 mt-4">
                    <h2 class="text-xl font-medium pb-1 mb-3 border-b border-b-slate-200">
                        {{ __('Keywords') }}
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($submission->tagsWithType('submissionKeywords')->pluck('name') as $keyword)
                            <span 
                                class="bg-slate-100 border border-slate-200 py-1 px-2 rounded-md link-primary flex items-center shadow-sm hover:bg-slate-200 hover:border-slate-300 transition ease-in-out duration-200">
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </section>
            <section clas="abstract">
                {{-- Abstract --}}
                <div class="text-slate-800 mt-4">
                    <h2 class="text-xl  font-medium pb-1 mb-3 border-b border-b-slate-200">
                        {{ __('Abstract') }}
                    </h2>
                    {!! $submission->getMeta('abstract') !!}
                </div>
            </section>
            <section class="references">
                <div class="text-slate-800 mt-4" id="references">
                    <h2 class="text-xl font-medium pb-1 mb-3 border-b border-b-slate-200">
                        {{ __('References') }}
                    </h2>
                    @if ($references = $submission->getMeta('references'))
                        {!! $references !!}
                    @else
                        <span class=" text-slate-400">
                            {{ __('No References') }}
                        </span>
                    @endif
                </div>
            </section>
            <section class="downloads">
                <div class="text-slate-800 mt-4">
                    <h2 class="text-xl">
                        {{ __('Downloads') }}
                    </h2>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach ($submission->getMedia(SubmissionFileCategory::EDITED_FILES) as $file)
                            <a href="{{ route('submission-files.view', $file->uuid) }}" target="_blank"
                                class="break-all bg-slate-100 border border-slate-200 py-1 px-3 rounded-md link-primary flex items-center shadow-sm hover:bg-slate-200 hover:border-slate-300 transition ease-in-out duration-200">
                                {{ $file->file_name }}
                                <x-lineawesome-external-link-square-alt-solid class="w-3 h-3 ml-0.5" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-website::layouts.main>
