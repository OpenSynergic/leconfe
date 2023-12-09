@use('App\Models\Enums\SubmissionStatus')
@use('App\Constants\SubmissionFileCategory')
<x-website::layouts.main>
    <div class="p-5">
        <div class="space-y-3">
            @foreach ($topics as $topic)
                <div class="font-semibold text-primary text-lg">
                    {{ $topic->name }}
                </div>
                @forelse($submissions = $topic->submissions()->published()->get() as $submission)
                    <div class="border-b border-b-slate-100 pb-4">
                        <a href="{{ route('livewirePageGroup.current-conference.pages.submission-detail', $submission->getKey()) }}" class="link-primary text-base">
                            {{ $submission->getMeta('title') }}
                        </a>

                        {{-- Contributors --}}
                        <div class="text-xs flex items-center text-slate-700">
                            <x-lineawesome-user class="w-3 h-3 mr-0.5"/>
                            {{ $submission->contributors()->with('participant')->get()->pluck('participant.fullName')->join(', ') }}
                        </div>
                        <div class="text-xs flex items-center text-slate-700">
                            <x-lineawesome-calendar-check-solid class="w-3 h-3 mr-0.5"/>
                            {{ __('Date Published')  . ': ' . $submission->published_at->format(setting('format.date')) }}
                        </div>
                        @if(($files = $submission->getMedia(SubmissionFileCategory::EDITED_FILES)) && $files->count())
                        <div class="flex flex-wrap gap-1.5 text-xs mt-2">
                            @foreach($files as $file)
                                <a href="{{ route('private.preview', $file->uuid ) }}" target="_blank" class="bg-slate-100 border border-slate-200 py-1 px-3 rounded-md link-primary flex items-center shadow-sm hover:bg-slate-200 hover:border-slate-300 transition ease-in-out duration-200">
                                    {{ $file->file_name }}
                                    <x-lineawesome-external-link-square-alt-solid class="w-3 h-3 ml-0.5"/>
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="text-xs text-gray-400">
                        No Submission
                    </div>
                @endforelse
            @endforeach
        </div>
    </div>
</x-website::layouts.main>
