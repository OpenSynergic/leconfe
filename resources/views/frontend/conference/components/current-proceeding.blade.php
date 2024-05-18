@use('App\Classes\Settings')
@props([
    'proceeding',
])

<div>
    <div class="flex mb-5 space-x-4">
        <div class="text-xl font-semibold min-w-fit">Current Proceeding</div>
        <hr class="w-full h-px my-auto bg-gray-200 border-0 dark:bg-gray-700">
    </div>
    <div class="grid grid-cols-9 gap-x-4">
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
</div>