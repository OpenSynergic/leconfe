@use('App\Models\Enums\SubmissionStatus')
@use('App\Constants\SubmissionFileCategory')
<x-website::layouts.main>
    <div class="submission-list p-5">
        @foreach ($topics as $topic)
            <div @class(['font-semibold text-2xl', 'mt-7' => !$loop->first])>
                {{ $topic->name }}
            </div>
            @forelse($submissions = $topic->submissions()->published()->get() as $submission)
                <div class="border-b border-b-slate-100 pb-4 mt-2">
                    <a href="{{ route('livewirePageGroup.conference.pages.submission-detail', $submission->getKey()) }}"
                        class="link-primary text-xl">
                        {{ $submission->getMeta('title') }}
                    </a>

                    <div class="text-sm flex items-center text-slate-700">
                        <x-lineawesome-user class="w-4 h-4 mr-0.5" />
                        {{ $submission->contributors()->with('participant')->get()->pluck('participant.fullName')->join(', ') }}
                    </div>
                    <div class="text-sm flex items-center text-slate-700">
                        <x-lineawesome-calendar-check-solid class="w-4 h-4 mr-0.5" />
                        {{ __('Date Published') . ': ' . $submission->published_at->format(Settings::get('date')) }}
                    </div>
                    @if (($files = $submission->getMedia(SubmissionFileCategory::EDITED_FILES)) && $files->count())
                        <div class="flex flex-wrap gap-1.5 text-sm mt-2">
                            @foreach ($files as $file)
                                <a href="{{ route('submission-files.view', $file->uuid) }}" target="_blank"
                                    class="break-all bg-slate-100 border border-slate-200 py-1 px-3 rounded-md link-primary flex items-center shadow-sm hover:bg-slate-200 hover:border-slate-300 transition ease-in-out duration-200">
                                    {{ $file->file_name }}
                                    <x-lineawesome-external-link-square-alt-solid class="w-4 h-4 ml-0.5" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class=" text-gray-400">
                    No Submission
                </div>
            @endforelse
        @endforeach
    </div>
</x-website::layouts.main>
