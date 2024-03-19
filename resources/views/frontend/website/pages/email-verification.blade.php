<x-website::layouts.main>
    <div class="card-body space-y-2">
        <h2 class="card-title"><x-heroicon-s-envelope class="h-5 w-5" /> Verify your email </h2>
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
            <button wire:click='sendEmailVerificationLink' class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                <span class="loading loading-spinner loading-xs" wire:loading></span>
                Resend Email
            </button>
        </div>
    </div>
</x-website::layouts.main>
