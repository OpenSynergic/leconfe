<x-website::layouts.main>
    <div class="card-body space-y-2">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        <form wire:submit='login' class="space-y-4">
            <div class="form-control sm:col-span-6 gap-2">
                <label class="label-text">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" class="input input-sm" wire:model="email" />
                @error('email')
                    <div class="text-red-600 text-sm">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-control sm:col-span-3 gap-2">
                <label class="label-text">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" class="input input-sm" wire:model="password" required />
                @error('password')
                    <div class="text-red-600 text-sm">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-control">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model='remember' class="checkbox checkbox-sm" />
                    <span class="label-text">Remember me</span>
                </label>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                    <span class="loading loading-spinner loading-xs" wire:loading></span>
                    Login
                </button>
                <x-website::link class="btn btn-outline btn-sm" :href="$registerUrl">
                    Register
                </x-website::link>
            </div>
        </form>
    </div>
</x-website::layouts.main>
