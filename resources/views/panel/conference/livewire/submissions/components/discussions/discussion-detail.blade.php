<div>
    <x-filament::fieldset class="mb-8">
    <x-slot name="label">
        {{ __("Participants") }}
    </x-slot>

    <div class="grid grid-cols-3 gap-4">
        @foreach($topic->participants()->with(['user', 'topic'])->get() as $participant)
        <div class="col-span-1 flex items-center space-x-2">
            <x-filament::avatar
                src="{{ $participant->user->getFilamentAvatarUrl() }}"
                alt="Profile Picture"
                :circular="true"
                size="w-12 h-12"
            />
            <div class="flex flex-col">
                {{ $participant->user->fullName }}
                <span class="text-sm text-gray-400">{{ $participant->getRoleName() }}</span>
            </div>
        </div>
        @endforeach
    </div>
    
    {{-- Form fields --}}
</x-filament::fieldset>
    {{ $this->table }}
</div>