<x-website::layouts.main>
    <div class="mb-6">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
    </div>
    <div class="relative">
        <div class="flex mb-5 space-x-4">
        <h1 class="text-xl font-semibold min-w-fit">{{ $this->getTitle() }}</h1>
            <hr class="w-full h-px my-auto bg-gray-200 border-0 dark:bg-gray-700">
        </div>
        @if(!$registerComplete)
                @if (Setting::get('allow_registration'))
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
                            
                            @if($currentConference)
                            <div class="form-control sm:col-span-6 gap-2">
                                <label class="label-text">Register as <span class="text-red-500">*</span></label>
                                @foreach ($roles as $role)
                                    <div class="form-control">
                                        <div class="inline-flex gap-2 items-center cursor">
                                            <input type="checkbox" class="checkbox checkbox-sm" wire:model='selfAssignRoles'
                                                value="{{ $role }}" />
                                            <label class="label-text">{{ $role }}</label>
                                        </div>
                                    </div>
                                @endforeach
                                @error('selfAssignRoles')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            @endif

                            @if(!$currentConference)
                                <div class="col-span-full space-y-4">
                                    <p class="">Which conferences listed on this website are you interested in registering for?</p>
                                    @foreach ($conferences as $conference)
                                        <div class="conference form-control gap-2">
                                            <label class="conference-name label-text font-medium">{{ $conference->name }}</label>
                                            @foreach ($roles as $role)
                                                <div class="conference-roles form-control">
                                                    <div class="inline-flex gap-2 items-center cursor">
                                                        <input type="checkbox" name="selfAssignRoles[{{ $conference->id }}]" class="checkbox checkbox-sm" wire:model='selfAssignRoles.{{ $conference->id }}.{{ $role }}'
                                                            value="{{ $role }}" />
                                                        <label class="label-text">{{ $role }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="form-control sm:col-span-6 gap-2">
                                <div class="form-control">
                                    <label class="p-0 label justify-normal gap-2">
                                        <input type="checkbox" class="checkbox checkbox-sm" wire:model="privacy_statement_agree"
                                            required />
                                        <p class="label-text">
                                            I accept and approve according to <x-website::link :href="$privacyStatementUrl" class="link link-primary link-hover">Privacy Statement.</x-website::link>
                                        </p>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                                <span class="loading loading-spinner loading-xs" wire:loading></span>
                                Register
                            </button>
                            <x-website::link class="btn btn-outline btn-sm" :href="url('login')">
                                Login
                            </x-website::link>
                        </div>
                    </form>
                @else
                    <p>This conference is currently closing user registrations</p>
                @endif
        @else
                <p>Thank you for completing the registration! What would you like to proceed with next?</p>
                <ul class='list-disc list-inside'> 
                    <li><x-website::link class="link link-primary link-hover" href="{{ $currentConference ? route('filament.conference.pages.profile') : route('filament.administration.pages.profile') }}">Edit My Profile</x-website::link></li>
                    <li><x-website::link class="link link-primary link-hover" href="{{ $homeUrl }}">Continue Browsing</x-website::link></li>
                </ul>
        @endif
    </div>
</x-website::layouts.main>
