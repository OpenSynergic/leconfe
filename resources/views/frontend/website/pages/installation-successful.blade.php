<div class="h-screen w-full max-w-4xl mx-auto flex flex-col" x-cloak>
    <div class="space-y-4 pt-16 pb-20 px-1 sm:px-4">
        <div class="avatar w-full">
            <div class="w-24 rounded-full mx-auto">
                <img src="{{ asset('logo.png') }}" />
            </div>
        </div>
        {{-- <p class="font-semibold ">Leconfe v{{ app()->getCodeVersion() }}</p> --}}
    </div>
    <div class="space-y-4 px-1 sm:px-4">
        <div class="text-center">
            <h1 class="text-2xl font-bold">Installation Successful</h1>
            <p class="text-lg">You have successfully installed Leconfe</p>
        </div>
        <div class="text-center">
            <a href="{{ route('livewirePageGroup.website.pages.home') }}" class="btn btn-primary">Go to Home</a>
        </div>
    </div>
</div>