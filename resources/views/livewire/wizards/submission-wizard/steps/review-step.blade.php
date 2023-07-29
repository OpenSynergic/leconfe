<div class="space-y-6">
    <div class="filament-forms-card-component p-6 bg-white rounded-xl border border-gray-300">
        <div class="grid grid-cols-1 filament-forms-component-container gap-6">
            <div class="col-span-full">
                <div id="upload-files" class="filament-forms-section-component grid grid-cols-1 md:grid-cols-2">
                    <div
                        class="filament-forms-section-header-wrapper flex rtl:space-x-reverse overflow-hidden rounded-t-xl min-h-[56px] pr-6 pb-4">
                        <div class="filament-forms-section-header flex-1 space-y-1">
                            <h3 class="font-bold tracking-tight pointer-events-none flex flex-row items-center text-xl">
                                Review and Submit
                            </h3>
                            <div class="space-y-4">
                                <p class="text-gray-500 text-base">
                                    Before submitting, please check the information you have entered and make any
                                    necessary changes by clicking the edit button at the top of each section.
                                </p>
                                <p class="text-gray-500 text-base">
                                    After you submit, one of our editorial team members will be assigned to review your
                                    submission. It is important that you enter the details as accurately as possible.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="filament-forms-section-content-wrapper space-y-6">
                        @if (session()->has('submission_not_complete'))
                            <div class="flex p-4 mb-4 text-sm text-danger-800 border border-danger-300 rounded-xl bg-danger-50 dark:bg-gray-800 dark:text-danger-400 dark:border-danger-800"
                                role="alert">
                                <svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor"
                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Info</span>
                                <div>
                                    {{ session('submission_not_complete') }}
                                </div>
                            </div>
                        @endif

                        <ul
                            class="w-full text-sm text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <li
                                class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600 justify-between flex items-center">
                                <div class="text-base font-bold">Details</div>
                                <x-filament::button class="" :outlined="true" size="sm"
                                    x-on:click="step = 'detail'">
                                    Edit
                                </x-filament::button>
                            </li>
                            <li class="w-full px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                <div class="font-medium">Title</div>
                                <div class="text-gray-700">{{ $this->record->getMeta('title') }}</div>
                            </li>
                            <li class="w-full px-4 py-2 border-b border-gray-200 dark:border-gray-600 space-y-1">
                                <div class="font-medium">Keywords</div>
                                <div class="flex flex-wrap items-center gap-1 text-gray-700">
                                    @forelse ($this->record->tagsWithType('submissionKeywords')->pluck('name')->toArray() as $tag)
                                        <span @class([
                                            'inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-sm tracking-tight rounded-xl text-primary-700 bg-primary-500/10 whitespace-normal',
                                            'dark:text-primary-500' => config('tables.dark_mode'),
                                        ])>
                                            {{ $tag }}
                                        </span>
                                    @empty
                                        No information has been provided.
                                    @endforelse
                                </div>
                            </li>
                            <li class="w-full px-4 py-2 border-gray-200 dark:border-gray-600">
                                <div class="font-medium">Abstract</div>
                                <div class="prose prose-sm">
                                    {!! $this->record->getMeta('abstract') !!}
                                </div>
                            </li>
                        </ul>
                        <ul
                            class="w-full text-sm text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <li
                                class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600 justify-between flex items-center">
                                <div class="text-base font-bold">Files</div>
                                <x-filament::button class="" :outlined="true" size="sm"
                                    x-on:click="step = 'upload-files'">
                                    Edit
                                </x-filament::button>
                            </li>
                            <li class="w-full px-4 py-2 border-gray-200 dark:border-gray-600">
                                @forelse ($this->record->getMedia('files') as $media)
                                    <div class="py-2">
                                        <a class="flex gap-2 hover:ring-1 hover:ring-primary-600 rounded w-fit"
                                            href="{{ $media?->getTemporaryUrl(now()->addMinutes(5)) }}">
                                            <x-heroicon-o-document-text class="w-5 h-5" /> {{ $media->file_name }}
                                        </a>
                                    </div>
                                @empty
                                    <div class="flex py-4 text-sm text-danger-800 rounded-xl dark:bg-gray-800"
                                        role="alert">
                                        <svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3"
                                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="sr-only">Info</span>
                                        <div>
                                            It is mandatory to upload at least one file.
                                        </div>
                                    </div>
                                @endforelse
                            </li>
                        </ul>
                        <ul
                            class="w-full text-sm text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <li
                                class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600 justify-between flex items-center">
                                <div class="text-base font-bold">Authors</div>
                                <x-filament::button class="" :outlined="true" size="sm"
                                    x-on:click="step = 'authors'">
                                    Edit
                                </x-filament::button>
                            </li>
                            @forelse ($this->record->authors as $author)
                                <li @class([
                                    'w-full px-4 py-2 border-gray-200 dark:border-gray-600 flex',
                                    'border-b' => !$loop->last,
                                ])>
                                    <div class="w-full">
                                        <p class="font-medium text-sm">{{ $author->public_name }}</p>
                                        <p class="text-xs">{{ $author->affiliation }}</p>
                                    </div>
                                    <div class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span class="">
                                            {{ $author->email }}
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="w-full px-4 py-2 border-gray-200 dark:border-gray-600">
                                    <div class="flex py-4 text-sm text-danger-800 rounded-xl dark:bg-gray-800"
                                        role="alert">
                                        <svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3"
                                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="sr-only">Info</span>
                                        <div>
                                            No contributors have been included for this submission.
                                        </div>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="flex items-center justify-between">
        <div>
            <x-forms::button icon="heroicon-o-chevron-left" x-show="! isFirstStep()" x-cloak x-on:click="previousStep"
                color="secondary" size="sm">
                {{ __('forms::components.wizard.buttons.previous_step.label') }}
            </x-forms::button>
        </div>
        <div>
            <x-forms::button x-cloak x-on:click="$dispatch('open-modal', {id: 'modalSubmisionWizardConfirmation'})"
                wire:loading.class.delay="opacity-70 cursor-wait" size="sm">
                Submit
            </x-forms::button>
        </div>
    </div>

    <x-filament-support::modal id="modalSubmisionWizardConfirmation">
        <x-slot name="header">
            <x-filament::modal.heading>
                Submit
            </x-filament::modal.heading>
        </x-slot>
        You are about to submit the <b>{{ $this->record->getMeta('title') }}</b> to <b>{{ setting('conference.title') }}</b> for editorial
        review. Are you sure you want
        to proceed with this submission?
        <x-slot name="footer" class="test">
            <div
                class="filament-modal-actions flex flex-wrap items-center gap-4 rtl:space-x-reverse flex-row-reverse space-x-reverse">
                <x-filament::button x-cloak wire:loading.class.delay="opacity-70 cursor-wait" size="sm"
                    wire:click="submit">
                    Yes, Submit
                </x-filament::button>
                <x-filament::button x-cloak color="secondary" :outlined="true"
                    x-on:click="$dispatch('close-modal', { id: 'modalSubmisionWizardConfirmation' })"
                    wire:loading.class.delay="opacity-70 cursor-wait" size="sm">
                    Cancel
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament-support::modal>
</div>
