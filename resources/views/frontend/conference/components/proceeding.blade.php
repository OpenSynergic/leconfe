@use('App\Classes\Settings')
@use('App\Models\Enums\SubmissionStatus')

@props([
    'proceeding',
    'title' => 'Current Proceeding'
])

@php
    $articles = $proceeding->submissions()->status(SubmissionStatus::Published)->get() ?? [];
@endphp
<div>
    <x-website::heading-title :title="$title" />
    <div class="grid grid-cols-9 mb-6 gap-x-4">
        @if($proceeding->getFirstMediaUrl('cover'))
            <div class="col-span-9 mb-5 md:max-w-64 max-w-[22rem] sm:col-span-2 sm:max-w-full sm:mb-0">
                <img src="{{ $proceeding->getFirstMediaUrl('cover') }}" class="w-full" alt="150">
            </div>
        @endif
        <div class="col-span-9 sm:col-span-7">
            <div class="space-y-4">
                <div class="text-sm font-semibold">
                    {{ $proceeding->seriesTitle() }}
                </div>
                <div class="text-sm text-justify">
                    {{ $proceeding->description }}
                </div>
                <div class="text-sm">
                    <span class="font-semibold">Published: </span> {{ $proceeding->published_at ? $proceeding->published_at->format(Settings::get('format_date')) : '-' }}
                </div>
            </div>
        </div>
    </div>
    <div class="my-8">
        <x-website::heading-title :title="'Articles'" />
        <div class="space-y-5">
            @forelse($articles as $article)
                <x-conference::article-summary :article="$article"/>  
            @empty
                <div class="text-center text-gray-500">
                    No articles found.
                </div>
            @endforelse
        </div>
    </div>
</div>