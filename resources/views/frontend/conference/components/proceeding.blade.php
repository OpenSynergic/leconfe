@use('App\Classes\Settings')
@props([
    'proceeding',
    'title' => 'Current Proceeding'
])

<div>
    <x-website::heading-title :title="$title" />
    <div class="grid grid-cols-9 mb-6 gap-x-4">
        @if($proceeding->getFirstMediaUrl('cover'))
            <div class="col-span-2 max-w-64">
                <img src="{{ $proceeding->getFirstMediaUrl('cover') }}" class="w-full" alt="150">
            </div>
        @endif
        <div class="col-span-7">
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
    @if ($proceeding->submissions()->exists())
        <div class="my-8">
            <x-website::heading-title :title="'Articles'" />
            <div class="space-y-5">
                @forelse($proceeding?->submissions()->get() as $article)
                    <x-conference::article-summary :article="$article"/>  
                @empty
                    
                @endforelse
            </div>
        </div>
    @endif
</div>