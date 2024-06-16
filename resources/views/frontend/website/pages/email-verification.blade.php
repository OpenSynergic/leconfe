<x-website::layouts.main>
    <div class="mb-6">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
    </div>
    <div class="relative">
        <div class="flex mb-5 space-x-4">
            <h1 class="text-xl font-semibold min-w-fit flex gap-2 items-center"><x-heroicon-s-envelope class="h-5 w-5" /> Verify your email </h1>
            <hr class="w-full h-px my-auto bg-gray-200 border-0 dark:bg-gray-700">
        </div>
        <div class="overflow-y-auto space-y-2">
            @if (session('success'))
                <div class="alert alert-warning">
                    <x-heroicon-s-envelope class="h-5 w-5" />
                    <span> Email verification link sent successfully.</span>
                </div>
            @endif
            @error('email')
                <div class="alert alert-error">
                    <x-heroicon-o-exclamation-circle class="stroke-current shrink-0 h-6 w-6" />
                    <span>{{ $message }}</span>
                </div>
            @enderror
            <p>Almost there! We've sent a verification email to <b>{{ Str::maskEmail(auth()->user()->email) }}</b>.</p>
            <p>You need to verify your email address to log into Leconfe.</p>
            <div>
                <button wire:click='sendEmailVerificationLink' class="btn btn-primary btn-sm"
                    wire:loading.attr="disabled">
                    <span class="loading loading-spinner loading-xs" wire:loading></span>
                    Resend Email
                </button>
            </div>
        </div>
    </div>
</x-website::layouts.main>
