@props(['article'])

@php
    $galleys = $article->galleys()->with('file.media')->get();
@endphp
<div class="grid grid-cols-10 space-x-0 sm:space-x-5">
    @if($article->getFirstMediaUrl('article-cover'))
        <a href="{{ route('livewirePageGroup.conference.pages.submission-detail', ['submissionId' => $article->id]) }}" class="col-span-10 max-w-96 md:col-span-3 sm:col-span-3 lg:col-span-2">
            <img class="w-full" src="{{ $article->getFirstMediaUrl('article-cover') }}" alt="">
        </a>
    @endif
    <div class="col-span-10 mt-2 md:col-span-7 sm:col-span-7 lg:col-span-8 sm:mt-0">
        <div class="mb-3 text-base">
            <a href="{{ route('livewirePageGroup.conference.pages.submission-detail', ['submissionId' => $article->id]) }}" class="font-semibold text-gray-700 hover:text-primary">{{ $article->getMeta('title') }}</a>
            <div class="text-xs text-gray-500">{{ $article->getMeta('subtitle') }}</div>
        </div>
        <div class="grid grid-cols-9 mb-3">
            <div class="w-full col-span-9 mb-1.5 text-xs sm:col-span-7 sm:mb-0 md:col-span-8">
                @foreach ($article->authors()->get() as $author)
                    <a href="#" class="text-gray-500 hover:text-primary">{{ $author->fullName }}</a>{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </div>
            @if($article->getMeta('article_pages'))
                <div class="col-span-9 text-xs text-right sm:col-span-2 md:col-span-1">
                    <div class="flex justify-start sm:justify-end gap-x-1">
                        <x-heroicon-o-document-text class="w-4 h-4 my-auto text-gray-500" />
                        <span>{{ $article->getMeta('article_pages') }}</span>
                    </div>
                </div>
            @endif
        </div>
        <div class="flex justify-start mb-3 text-xs sm:justify-end sm:mb-0">
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
            @if($article->doi)
                <a href="#" class="flex space-x-1 text-gray-500 hover:text-primary">
                    <x-academicon-doi class="w-4 h-4 my-auto text-yellow-400" />
                    <span>DOI : {{ $article->doi->doi }}</span>
                </a>
            @endif
        </div>
        @if($galleys->isNotEmpty())
            <div class="flex space-x-1.5">
                @foreach ($galleys as $galley)
                    <x-conference::galley-summary :label="$galley->label" :url="$galley->remote_url ?? route('submission-files.view', $galley->file->media->uuid)"/>
                @endforeach
            </div>
        @endif
    </div>
</div>