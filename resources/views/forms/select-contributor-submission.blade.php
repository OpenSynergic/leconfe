<div class="flex flex-col space-y-1">
    <div class="text-xs font-normal">{{ $contributor->submission->getMeta('title') }}</div>
    <div class="flex items-center gap-4 cursor-pointer">
        <div style="height: 50px; width: 50px;">
            <img src="{{ $contributor->getFilamentAvatarUrl() }}" style="height: 50px; width: 50px;" class="object-cover object-center rounded-full max-w-none ring-white dark:ring-gray-900">
        </div>
        <div>
            <p>{{ $contributor->given_name }} {{ $contributor->family_name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-300">{{ $contributor->email }}</p>
        </div>
    </div>
</div>