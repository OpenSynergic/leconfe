<div>
    <div class="text-sm space-y-2 ">
        @forelse($getRecord()->getMedia('discussion-attachment') as $attachment)
            <a href="{{ route('private.files', $attachment->uuid) }}" target="_blank"
                class="text-primary-600 flex justify-center hover:text-primary-800">
                <x-lineawesome-file-alt-solid class="w-4 h-4 mr-2" />
                {{ $attachment->name }}
            </a>
        @empty
            <span class="text-sm text-gray-600">{{ __('No Attachments Found') }}</span>
        @endforelse
    </div>

</div>
