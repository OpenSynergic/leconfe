<div class="flex items-center gap-4 cursor-pointer">
    @if($participant->hasMedia('photo'))
    <div style="height: 50px; width: 50px;">
        <img src="{{ $participant->getFirstMediaUrl('photo', 'avatar') }}" style="height: 50px; width: 50px;" class="max-w-none object-cover object-center rounded-full ring-white dark:ring-gray-900">
    </div>
    @endif
    <div>
        <p>{{ $participant->given_name }} {{ $participant->family_name }}</p>
        <p class="text-sm text-gray-500">{{ $participant->email }}</p>
    </div>
</div>