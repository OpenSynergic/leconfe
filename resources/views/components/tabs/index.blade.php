@props([
  'buttons',
])

<div x-data="tabcomponent" x-id="['alpinetabs']" class="space-y-4 overflow-auto">
  <nav {{ $attributes->class([
    'flex items-center p-1 space-x-1 rtl:space-x-reverse text-sm text-gray-600 bg-gray-500/5 rounded-xl filament-tabs w-fit',
    'dark:bg-gray-500/20' => config('filament.dark_mode'),
  ]) }}>
    {{ $buttons }}
  </nav>

  <div>
    {{ $slot }}
  </div>
</div>