<div class="flex justify-between items-center">
    {{-- Split being component --}}
    <div class="max-w-sm p-6">
        <p class="py-3">{{ $conference_date }}</p>
        <a href="#">
            <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">{{ $conference_title }}</h5>
        </a>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ $conference_description ?? '' }}</p>
        </a>
    </div>

    <div class="max-w-sm p-6">
        {{ $conference_type ?? '' }}
    </div>

    {{-- split being component --}}
    <div class="">
      {{ $conference_browsur ??'' }}
    </div>
</div>
