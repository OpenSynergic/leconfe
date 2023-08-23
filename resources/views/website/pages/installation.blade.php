<div class="h-screen w-full max-w-4xl mx-auto flex flex-col" x-cloak>
    <div class="space-y-4 pt-16 pb-20 px-1 sm:px-4">
        <h1 class="text-center text-4xl font-medium mb-10">Logo Leconfe</h1>
        <p class="font-semibold ">Version {{ app()->getAppVersion() }}</p>
        <form wire:submit='install'>
            <div class="card bg-white text-sm">
                <div class="card-body space-y-6">
                    @error('install')
                        <div class="alert alert-error">
                            <x-heroicon-o-exclamation-circle class="stroke-current shrink-0 h-6 w-6" />
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                    <div class="introduction">
                        Thank you for downloading the Leconfe, a project by <a class="link link-primary link-hover"
                            href="https://openjournaltheme.com">Open Journal Theme</a>
                    </div>
                    <div class="system-requirement space-y-2">
                        <h2 class="text-lg not-italic font-semibold leading-7 text-black">System Requirements</h2>
                        <p class="w-full text-sm not-italic leading-snug ">Leconfe has a few server
                            requirements. Make sure that your server has the required php version and other requirement
                            mentioned below.</p>
                        <ul class="max-w-2xl text-sm bg-white border border-gray-200 rounded-lg">
                            <li
                                class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg flex justify-between flex-wrap">
                                <div>
                                    <a class="link link-primary link-hover" target="_blank" href="https://php.net">PHP</a>
                                    >= {{ app()->getPhpMinVersion() }}
                                </div>
                                <div class="flex gap-2">
                                    <span>Current Version : <b>{{ PHP_VERSION }}</b></span>
                                </div>
                            </li>
                            <li class="w-full px-4 py-2 border-gray-200 rounded-t-lg flex justify-between flex-wrap">
                                <div>
                                    <a class="link link-primary link-hover" target="_blank"
                                        href="https://mysql.com">MySQL</a> >= 5.7
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="folder-permission">
                        <div class="mt-2.5 mb-6 space-y-2">
                            <h2 class="text-lg not-italic font-semibold leading-7 text-black">Permissions</h2>
                            <p class="w-full text-sm not-italic leading-snug ">
                                Below is the list of folder permissions which are required in order for the app to work. If
                                the permission check fails, make sure to update your folder permissions.
                            </p>
                        </div>
                        @if (!empty($this->folders))
                            <ul class="max-w-2xl text-sm bg-white border border-gray-200 rounded-lg">
                                @foreach ($this->folders as $folder => $status)
                                    <li @class([
                                        'w-full px-4 py-2 border-gray-200 flex justify-between flex-wrap',
                                        'rounded-t-lg' => $loop->first,
                                        'rounded-b-lg' => $loop->last,
                                        'border-b' => !$loop->last,
                                    ])>
                                        <div>
                                            {{ $folder }}
                                        </div>
                                        <div>
                                            @if ($status)
                                                <x-heroicon-s-check-circle class="text-primary w-5 h-5" />
                                            @else
                                                <x-heroicon-s-x-circle class="text-red-500 w-5 h-5" />
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="account">
                        <div class="mt-2.5 mb-6 space-y-2">
                            <h2 class="text-lg not-italic font-semibold leading-7 text-black">Account Information</h2>
                            <p class="w-full text-sm not-italic leading-snug">
                                This user account will become your admin account and have complete access to the system. Also you can change the details anytime after logging in.
                            </p>
                        </div>
                        <div class="space-y-4">
                            <div class="grid sm:grid-cols-6 gap-4">
                                <div class="form-control sm:col-span-3 gap-2">
                                    <label class="label-text">
                                        Given Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" class="input input-bordered input-sm"
                                    wire:model="account.given_name" required/>
                                    @error('account.given_name')
                                        <div class="text-red-600 text-sm">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-control sm:col-span-3 gap-2">
                                    <label class="label-text">
                                        Family Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" class="input input-bordered input-sm"
                                    wire:model="account.family_name" required/>
                                    @error('account.family_name')
                                        <div class="text-red-600 text-sm">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-control sm:col-span-6 gap-2">
                                    <label class="label-text">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" class="input input-bordered input-sm"
                                    wire:model="account.email" />
                                    @error('account.email')
                                        <div class="text-red-600 text-sm">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-control sm:col-span-3 gap-2">
                                    <label class="label-text">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" class="input input-bordered input-sm"
                                    wire:model="account.password" required/>
                                    @error('account.password')
                                        <div class="text-red-600 text-sm">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-control sm:col-span-3 gap-2">
                                    <label class="label-text">
                                        Password Confirmation <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" class="input input-bordered input-sm"
                                    wire:model="account.password_confirmation" required />
                                    @error('account.password_confirmation')
                                        <div class="text-red-600 text-sm">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="database space-y-4">
                        <div class="mt-2.5 mb-6 space-y-2">
                            <h2 class="text-lg not-italic font-semibold leading-7 text-black">Database</h2>
                            <p class="w-full text-sm not-italic leading-snug ">
                                Leconfe needs to access a SQL database to save its information. You can find the list of supported databases in the system requirements mentioned above. In the spaces provided below, please input the necessary settings for establishing a connection to the database.
                            </p>
                        </div>
                        @error('database.checkConnection')
                            <div class="alert alert-error">
                                <x-heroicon-o-exclamation-circle class="stroke-current shrink-0 h-6 w-6" />
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Database Connection <span class="text-red-500">*</span>
                                </label>
                                <select class="select select-sm select-bordered" wire:model="database.connection" required>
                                    <option value="mysql">MySQL</option>
                                </select>
                                @error('database.connection')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Database Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="input input-bordered input-sm" wire:model="database.name" required/>
                                @error('database.name')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Database Username <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="input input-bordered input-sm"
                                    wire:model="database.username" required/>
                                @error('database.username')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Database Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" class="input input-bordered input-sm"
                                    wire:model="database.password" required/>
                                @error('database.password')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Database Host <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="input input-bordered input-sm" wire:model="database.host" required/>
                                @error('database.host')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Database Port <span class="text-red-500">*</span>
                                </label>
                                <input type="number" class="input input-bordered input-sm" wire:model="database.port" required/>
                                @error('database.port')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="conference space-y-4">
                        <div class="mt-2.5 mb-6 space-y-2">
                            <h2 class="text-lg not-italic font-semibold leading-7 text-black">Conference</h2>
                            <p class="w-full text-sm not-italic leading-snug">
                                Create your first conference.
                            </p>
                        </div>
                        <div class="grid gap-4">
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Conference Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="input input-bordered input-sm" wire:model="conference.name" required/>
                                @error('conference.name')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Conference Type <span class="text-red-500">*</span>
                                </label>
                                <select class="select select-sm select-bordered" wire:model="conference.type">
                                    @foreach (\App\Models\Enums\ConferenceType::array() as $key => $type)
                                        <option value="{{ $key }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('conference.type')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-16">
                        <button type="submit" class="btn btn-primary btn-outline btn-sm ml-auto" wire:loading.attr="disabled">
                            <span class="loading loading-spinner loading-xs" wire:loading></span>
                            Install Leconfe
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
