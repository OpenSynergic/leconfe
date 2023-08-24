<div class="h-screen w-full max-w-6xl mx-auto flex flex-col" x-cloak>
    <div class="space-y-4">
        <h1 class="text-center text-4xl font-medium mb-10">Logo Leconfe</h1>
        <div class="stepper bg-white rounded-xl p-4 sm:p-6 space-y-4 sm:space-y-6">
            <ol
                class="flex flex-wrap sm:flex-nowrap items-center w-full text-sm font-medium text-center text-gray-500 dark:text-gray-400 sm:text-base">
                <li :class="$wire.currentStep == 'requirements' && 'text-primary dark:text-primary'"
                    x-on:click="$wire.currentStep = 'requirements'"
                    class="flex md:w-full items-center sm:after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                    <span
                        class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                        Requirements
                    </span>
                </li>
                <li :class="$wire.currentStep == 'folder_permission' && 'text-primary dark:text-primary'"
                    x-on:click="$wire.currentStep = 'folder_permission'"
                    class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                    <span
                        class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                        Permissions
                    </span>
                </li>
                <li :class="$wire.currentStep == 'database' && 'text-primary dark:text-primary'"
                    x-on:click="$wire.currentStep = 'database'"
                    class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                    <span
                        class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                        Database
                    </span>
                </li>
                <li :class="$wire.currentStep == 'account' && 'text-primary dark:text-primary'"
                    x-on:click="$wire.currentStep = 'account'"
                    class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                    <span
                        class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                        Account
                    </span>
                </li>
                <li :class="$wire.currentStep == 'conference' && 'text-primary dark:text-primary'"
                    x-on:click="$wire.currentStep = 'conference'"
                    class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                    <span
                        class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                        Conference
                    </span>
                </li>
                <li :class="$wire.currentStep == 'confirmation' && 'text-primary dark:text-primary'"
                    x-on:click="$wire.currentStep = 'confirmation'" class="flex items-center">
                    <span class="">
                        Confirmation
                    </span>
                </li>
            </ol>
            <hr />
            <div x-cloak>
                <div x-show="$wire.currentStep == 'requirements'" class="space-y-4">
                    <div class="mt-2.5 mb-6 sm:w-7/12 space-y-2">
                        <h2 class="text-2xl not-italic font-semibold leading-7 text-black">System Requirements</h2>
                        <p class="w-full text-sm not-italic leading-snug text-gray-500 ">Leconfe has a few server
                            requirements. Make sure that your server has the required php version and all the extensions
                            mentioned below.</p>
                    </div>
                    @if (!empty($this->requirements))
                        <ul
                            class="max-w-2xl text-sm text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <li
                                class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600 flex justify-between">
                                <div>
                                    PHP (version {{ app()->getPhpMinVersion() }} required)
                                </div>
                                <div class="flex gap-2">
                                    <span>{{ PHP_VERSION }}</span>

                                    @if ($this->requirements['php'])
                                        <x-heroicon-s-check-circle class="text-primary w-5 h-5" />
                                    @else
                                        <x-heroicon-s-x-circle class="text-red-500 w-5 h-5" />
                                    @endif
                                </div>
                            </li>

                            @foreach ($this->requirements['php_extensions'] as $extension => $enabled)
                                <li @class([
                                    'w-full px-4 py-2 border-gray-200 dark:border-gray-600 flex justify-between',
                                    'rounded-b-lg' => $loop->last,
                                    'border-b' => !$loop->last,
                                ])>
                                    <div>
                                        {{ $extension }}
                                    </div>
                                    <div>
                                        @if ($enabled)
                                            <x-heroicon-s-check-circle class="text-primary w-5 h-5" />
                                        @else
                                            <x-heroicon-s-x-circle class="text-red-500 w-5 h-5" />
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="flex justify-between">
                        @if (empty($this->requirements))
                            <button type="button" wire:loading.attr="disabled" class="btn btn-primary btn-sm"
                                wire:click='checkRequirement'>
                                Check Requirements
                            </button>
                        @endif

                        @if ($this->isRequirementMet() && !empty($this->requirements))
                            <button type="button" class="btn btn-primary btn-sm" wire:click="stepRequirement">
                                <x-heroicon-o-arrow-right class="w-5 h-5" /> Continue
                            </button>
                        @endif
                    </div>
                </div>
                <div x-show="$wire.currentStep == 'folder_permission'" class="space-y-4">
                    <div class="mt-2.5 mb-6 sm:w-7/12 space-y-2">
                        <h2 class="text-2xl not-italic font-semibold leading-7 text-black">Permissions</h2>
                        <p class="w-full text-sm not-italic leading-snug text-gray-500 ">
                            Below is the list of folder permissions which are required in order for the app to work. If
                            the permission check fails, make sure to update your folder permissions.
                        </p>
                    </div>
                    @if (!empty($this->folders))
                        <ul
                            class="max-w-2xl text-sm text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach ($this->folders as $folder => $status)
                                <li @class([
                                    'w-full px-4 py-2 border-gray-200 dark:border-gray-600 flex justify-between',
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
                    <div class="flex justify-between">
                        <button type="button" class="btn btn-primary btn-sm" wire:click="stepPermission">
                            <x-heroicon-o-arrow-right class="w-5 h-5" /> Continue
                        </button>
                    </div>
                </div>
                <div x-show="$wire.currentStep == 'database'" class="space-y-6">
                    <div class="mt-2.5 mb-6 sm:w-7/12 space-y-2">
                        <h2 class="text-2xl not-italic font-semibold leading-7 text-black">Database</h2>
                        <p class="w-full text-sm not-italic leading-snug text-gray-500 ">
                            Create a database on your server and set the credentials using the form below.
                        </p>
                    </div>
                    @error('database.checkConnection')
                    <div class="alert alert-warning" x-cloak>
                        <x-heroicon-o-exclamation-circle class="stroke-current shrink-0 h-6 w-6"/>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                    <form wire:submit='stepDatabase' class="space-y-4">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="form-control gap-2">
                                <label class="label-text">
                                    Database Connection <span class="text-red-500">*</span>
                                </label>
                                <select class="select select-sm select-bordered" wire:model="database.connection">
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
                                <input type="text" class="input input-bordered input-sm"
                                    wire:model="database.name" />
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
                                    wire:model="database.username" />
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
                                    wire:model="database.password" />
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
                                <input type="text" class="input input-bordered input-sm"
                                    wire:model="database.host" />
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
                                <input type="number" class="input input-bordered input-sm"
                                    wire:model="database.port" />
                                @error('database.port')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                                <x-heroicon-o-arrow-right class="w-5 h-5" /> Continue
                            </button>
                        </div>
                    </form>
                </div>
                <div x-show="$wire.currentStep == 'account'" class="space-y-6">
                    <div class="mt-2.5 mb-6 sm:w-7/12 space-y-2">
                        <h2 class="text-2xl not-italic font-semibold leading-7 text-black">Account Information</h2>
                        <p class="w-full text-sm not-italic leading-snug text-gray-500 ">
                            Create your admin account using the form below. Also you can change the details anytime after logging in.
                        </p>
                    </div>
                    <form wire:submit='stepAccount' class="space-y-4">
                        <div class="grid sm:grid-cols-6 gap-4">
                            <div class="form-control sm:col-span-2 gap-2">
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
                            <div class="form-control sm:col-span-2 gap-2">
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
                            <div class="form-control sm:col-span-2 gap-2">
                                <label class="label-text">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" class="input input-bordered input-sm"
                                wire:model="account.email" required/>
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
                                wire:model="account.password_confirmation" required/>
                                @error('account.password_confirmation')
                                    <div class="text-red-600 text-sm">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                                <x-heroicon-o-arrow-right class="w-5 h-5" /> Continue
                            </button>
                        </div>
                    </form>

                </div>
                <div x-show="$wire.currentStep == 'conference'">
                    <input type="text" placeholder="Type here" class="input input-bordered input-sm" />
                </div>
                <div x-show="$wire.currentStep == 'confirmation'">
                    <input type="text" placeholder="Type here" class="input input-bordered input-sm" />
                </div>
            </div>
        </div>
    </div>
</div>
