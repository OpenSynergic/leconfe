<div class="flex flex-col gap-4">
    <div>
      <h3 class="text-base font-semibold">{{ $participan ?? '' }}</h3>
      <p class="text-sm font-normal text-gray-400 dark:text-gray-500">{{ $participan_description ?? '' }}</p>
    </div>
    <div>
        <h3 class="text-base font-semibold">{{ $submission ?? '' }}</h3>
        <p class="text-sm font-normal text-gray-400 dark:text-gray-500">{{ $submission_description ?? '' }}</p>
        {{ $slot }}
    </div>
</div>
