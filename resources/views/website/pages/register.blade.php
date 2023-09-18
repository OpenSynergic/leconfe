<x-conference::layouts.main>
    <div class="card-body space-y-2">
        <x-conference::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        <h2 class="card-title">{{ $this->getTitle() }}</h2>

        <form wire:submit='register' class="space-y-4">
            <div class="grid sm:grid-cols-6 gap-4">
                <div class="form-control sm:col-span-3 gap-2">
                    <label class="label-text">
                        Given Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" class="input input-sm" wire:model="given_name" required />
                    @error('given_name')
                        <div class="text-red-600 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-control sm:col-span-3 gap-2">
                    <label class="label-text">
                        Family Name
                    </label>
                    <input type="text" class="input input-sm" wire:model="family_name" />
                    @error('family_name')
                        <div class="text-red-600 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-control sm:col-span-3 gap-2">
                    <label class="label-text">
                        Affiliation
                    </label>
                    <input type="text" class="input input-sm" wire:model="affiliation" />
                    @error('affiliation')
                        <div class="text-red-600 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-control sm:col-span-3 gap-2">
                    <label class="label-text">
                        Country
                    </label>
                    <select class="select select-sm font-normal" name="country" wire:model='country'>
                        <option value="none" selected disabled>Select country</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->flag . ' ' . $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="text-red-600 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-control sm:col-span-6 gap-2">
                    <label class="label-text">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" class="input input-sm" wire:model="email" />
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
                    <input type="password" class="input input-sm" wire:model="password" required />
                    @error('password')
                        <div class="text-red-600 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-control sm:col-span-3 gap-2">
                    <label class="label-text">
                        Password Confirmation <span class="text-red-500">*</span>
                    </label>
                    <input type="password" class="input input-sm" wire:model="password_confirmation" required />
                    @error('password_confirmation')
                        <div class="text-red-600 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-control sm:col-span-6 gap-2">
                    <div class="form-control">
                        <label class="label justify-normal gap-2">
                            <input type="checkbox" class="checkbox checkbox-primary checkbox-sm" wire:model="privacy_statement_agree" required/>
                            <p class="label-text">
                                I accept and approve according to <x-conference::link href="#" class="link link-primary link-hover">Privacy Statement.</x-conference::link>
                            </p>
                        </label>
                    </div>
                    @error('password_confirmation')
                        <div class="text-red-600 text-sm">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                    <span class="loading loading-spinner loading-xs" wire:loading></span>
                    Register
                </button>
                <x-conference::link class="btn btn-outline btn-sm" :href="url('login')">
                    Login
                </x-conference::link>
            </div>
        </form>
    </div>
</x-conference::layouts.main>
