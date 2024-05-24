@use('App\Models\Enums\SubmissionStatus')
@use('App\Constants\SubmissionFileCategory')
<x-website::layouts.main>
    <div class="p-5 submission-list">
        @foreach ($topics as $topic)
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
                        {{ __('Date Published') . ': ' . $submission->published_at->format(Setting::get('format_date')) }}
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
        @endforeach
    </div>
</x-website::layouts.main>
