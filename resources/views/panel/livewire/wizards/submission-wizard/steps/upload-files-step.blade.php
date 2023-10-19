<div class="space-y-6">
    <div class="filament-forms-card-component p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="grid grid-cols-1 filament-forms-component-container gap-6">
            <div class="col-span-full">
                <div id="upload-files" class="filament-forms-section-component grid grid-cols-1 md:grid-cols-2">
                    <div
                        class="filament-forms-section-header-wrapper flex rtl:space-x-reverse overflow-hidden min-h-[56px] pr-6 pb-4">
                        <div class="filament-forms-section-header flex-1 space-y-1">
                            <h3 class="font-bold tracking-tight pointer-events-none flex flex-row items-center text-xl">
                                Upload Files
                            </h3>

                            <p class="text-gray-500 text-base">
                                Please include any necessary files for our editorial team to evaluate your submission. Along with the primary work, you may also choose to submit supplementary files such as data sets, conflict of interest statements, or other relevant materials that could assist our editors.
                            </p>
                        </div>
                    </div>
                        @livewire(App\Panel\Livewire\Submissions\Components\Files\SupplementaryFiles::class, ['submission' => $record])
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <x-filament::button icon="heroicon-o-chevron-left" x-show="! isFirstStep()" x-cloak x-on:click="previousStep"
                color="gray" size="sm">
               Previous
            </x-filament::button>
        </div>
        <div>
            {{ $this->nextStep() }}
        </div>
    </div>
</div>
