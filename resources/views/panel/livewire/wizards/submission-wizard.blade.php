<div x-data="{

    step: null,

    stepsData: @js($this->getStepKeys()),

    init: function() {
        this.$watch('step', () => this.updateQueryString())

        this.step = this.getSteps()[@js($this->getStartStep()) - 1]
    },

    nextStep: function() {
        let nextStepIndex = this.getStepIndex(this.step) + 1

        if (nextStepIndex >= this.getSteps().length) {
            return
        }

        this.step = this.getSteps()[nextStepIndex]

        this.autofocusFields()
        this.scrollToTop()
    },

    previousStep: function() {
        let previousStepIndex = this.getStepIndex(this.step) - 1

        if (previousStepIndex < 0) {
            return
        }

        this.step = this.getSteps()[previousStepIndex]

        this.autofocusFields()
        this.scrollToTop()
    },

    scrollToTop: function() {
        this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    },

    autofocusFields: function() {
        $nextTick(() => this.$refs[`step-${this.step}`].querySelector('[autofocus]')?.focus())
    },

    getStepIndex: function(step) {
        return this.getSteps().findIndex((indexedStep) => indexedStep === step)
    },

    getSteps: function() {
        return this.stepsData
    },

    isFirstStep: function() {
        return this.getStepIndex(this.step) <= 0
    },

    isLastStep: function() {
        return (this.getStepIndex(this.step) + 1) >= this.getSteps().length
    },

    isStepAccessible: function(step, index) {
        return false || (this.getStepIndex(step) > index)
    },

    updateQueryString: function() {
        const url = new URL(window.location.href)
        url.searchParams.set('step', this.step)

        history.pushState(null, document.title, url.toString())
    },

}"
    x-on:next-wizard-step.window="nextStep()" x-cloak
    class="space-y-6">
    <ol role="list"
        class=" shadow-sm bg-white rounded-xl overflow-hidden divide-y divide-gray-300 md:flex md:divide-y-0 dark:bg-gray-900 dark:ring-white/10">
        @foreach ($this->steps() as $key => $step)
            <li class="relative overflow-hidden group md:flex-1" x-data="{
                isActive(){
                    return getStepIndex(step) === {{ $loop->index }}
                } 
            }">
                <button type="button"
                    x-on:click="if (isStepAccessible(step, {{ $loop->index }})) step = '{{ $key }}'"
                    x-bind:aria-current="isActive() ? 'step' : null"
                    x-bind:class="{
                        'cursor-not-allowed pointer-events-none': !isStepAccessible(step, {{ $loop->index }}),
                    }"
                    role="step" class="flex items-center w-full h-full text-start">
                    <div x-bind:class="{
                        'bg-transparent  @if (config('forms.dark_mode')) dark:group-hover:bg-gray-600 @endif': getStepIndex(
                            step) > {{ $loop->index }},
                    }"
                        class="absolute top-0 left-0 w-1 h-full md:w-full md:h-1 md:bottom-0 md:top-auto"
                        aria-hidden="true"></div>

                    <div class="flex items-center gap-3 px-5 py-4 text-sm font-medium">
                        <div class="flex-shrink-0">
                            <div x-bind:class="{
                                'bg-primary-600': getStepIndex(step) > {{ $loop->index }},
                                'border-2': getStepIndex(step) <= {{ $loop->index }},
                                '!border-primary-500': isActive(),
                            }"
                                class="flex items-center justify-center w-10 h-10 rounded-full border-gray-700">
                                <x-heroicon-o-check x-show="getStepIndex(step) > {{ $loop->index }}" x-cloak
                                    class="w-5 h-5 text-white" />

                                <span x-show="getStepIndex(step) <= {{ $loop->index }}"
                                    x-bind:class="{
                                        'text-primary-500': isActive(),
                                    }" class="text-gray-500">
                                    {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col items-start justify-center">
                            <div class="text-sm font-semibold tracking-wide" x-bind:class="{
                                'text-primary-500': isActive(),
                            }">
                                {{ $step::getWizardLabel() }}
                            </div>
                        </div>
                    </div>
                </button>

                @if (!$loop->last)
                <div
                        aria-hidden="true"
                        class="absolute top-0 end-0 hidden w-5 md:block"
                    >
                        <svg
                            fill="none"
                            preserveAspectRatio="none"
                            viewBox="0 0 22 80"
                            class="h-full w-full text-gray-200 rtl:rotate-180 dark:text-white/5"
                        >
                            <path
                                d="M0 -2L20 40L0 82"
                                stroke-linejoin="round"
                                stroke="currentcolor"
                                vector-effect="non-scaling-stroke"
                            ></path>
                        </svg>
                    </div>
                    {{-- <div class="absolute inset-0 top-0 left-0 hidden w-3 md:block" aria-hidden="true">
                        <svg @class([
                            'h-full w-full text-gray-800 rtl:rotate-180',
                            'dark:text-gray-900' => config('forms.dark_mode'),
                        ]) viewBox="0 0 12 82" fill="none" preserveAspectRatio="none">
                            <path d="M0.5 0V31L10.5 41L0.5 51V82" stroke="currentcolor"
                                vector-effect="non-scaling-stroke" />
                        </svg>
                    </div> --}}
                @endif
            </li>
        @endforeach

    </ol>


    <div class="form">
        @foreach ($this->steps() as $key => $step)
            <div aria-labelledby="{{ $key }}" id="{{ $key }}" x-ref="step-{{ $key }}"
                role="tabpanel" tabindex="0"
                x-bind:class="{ 'invisible h-0 overflow-y-hidden': step !== @js($key) }"
                x-on:expand-concealing-component.window="
            error = $el.querySelector('[data-validation-error]')
    
            if (! error) {
                return
            }
    
            if (! isStepAccessible(step, @js($key))) {
                return
            }
    
            step = @js($key)
    
            if (document.body.querySelector('[data-validation-error]') !== error) {
                return
            }
    
            setTimeout(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'start' }), 200)
        "
                class="filament-forms-wizard-component-step outline-none">
                @livewire($step, ['record' => $this->record])
            </div>
        @endforeach
    </div>
</div>
