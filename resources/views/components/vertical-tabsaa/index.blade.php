@props([
  'buttons',
])

<div x-data="tabcomponent" x-id="['alpinetabs']"
    {{ $attributes->merge([
      'class' => 'flex flex-col sm:flex-row rounded-xl shadow-sm border border-gray-300 bg-white filament-forms-tabs-vertical-component dark:bg-gray-700 dark:border-gray-600'
    ]) }}>
    <div
      x-bind="tablist" role="tablist" 
      role="tablist" 
      x-ref="tablist"
      {{ $buttons->attributes->class(['rounded-tl-xl rounded-tr-xl sm:rounded-tr-none sm:rounded-l-xl flex w-full sm:w-auto sm:shrink min-w-[200px] pr-4 pl-4 py-4 sm:pr-0 flex-col overflow-y-auto']) }}
      >
        {{ $buttons }}
    </div>
    <div class="grow py-4 pr-4">
        {{ $slot }}
    </div>
</div>