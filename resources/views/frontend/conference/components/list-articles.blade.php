@props(['articles'])
<div>
    <div class="flex mb-5 space-x-4">
        <div class="text-xl font-semibold min-w-fit">Articles</div>
        <hr class="w-full h-px my-auto bg-gray-200 border-0 dark:bg-gray-700">
    </div>
    <div class="space-y-5">
        @foreach ($articles as $article) 
            @php
                $galleys = $article->galleys()->with('file.media')->get();
                $doi = $article->doi;
            @endphp
            <div class="grid grid-cols-10 space-x-5">
                @if($article->getFirstMediaUrl('article-cover'))
                    <a href="{{ route('livewirePageGroup.conference.pages.submission-detail', ['submissionId' => $article->id]) }}" class="col-span-2 max-h-36">
                        <img class="w-full h-full" src="{{ $article->getFirstMediaUrl('article-cover') }}" alt="">
                    </a>
                @endif
                <div class="col-span-8">
                    <div class="mb-3 text-base">
                        <a href="{{ route('livewirePageGroup.conference.pages.submission-detail', ['submissionId' => $article->id]) }}" class="font-semibold text-gray-700 hover:text-primary">{{ $article->getMeta('title') }}</a>
                        <div class="text-xs text-gray-500">{{ $article->getMeta('subtitle') }}</div>
                    </div>
                    <div class="grid grid-cols-9 mb-3">
                        <div class="w-full col-span-8 text-xs">
                            @foreach ($article->authors()->get() as $author)
                                <a href="#" class="text-gray-500 hover:text-primary">{{ $author->fullName }}</a>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                        @if($article->getMeta('article_pages'))
                            <div class="col-span-1 text-xs text-right">
                                <div class="flex justify-end gap-x-1">
                                    <x-heroicon-o-document-text class="w-4 h-4 my-auto text-gray-500" />
                                    <span>{{ $article->getMeta('article_pages') }}</span>
                                </div>
                            </div>
                        @endif
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
                        @if($doi)
                            <a href="#" class="flex space-x-1 text-gray-500 hover:text-primary">
                                <x-academicon-doi class="w-4 h-4 my-auto text-yellow-400" />
                                <span>DOI : {{ $doi->doi }}</span>
                            </a>
                        @endif
                    </div>
                    @if($galleys->isNotEmpty())
                        <div class="flex space-x-1.5">
                            @foreach ($galleys as $galley) 
                                <a 
                                    href="{{ !$galley->remote_url ? route('submission-files.view', $galley->file->media->uuid) : $galley->remote_url }}" 
                                    class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8"
                                >
                                    {{ $galley->label }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>