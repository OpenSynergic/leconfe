@props([
    'contained' => false,
    'label' => null,
])

<div
    {{
        $attributes
            ->merge([
                'aria-label' => $label,
                'role' => 'tablist',
            ])
            ->class([
                'flex flex-col w-56 dark:bg-gray-900 rounded-xl shadow-sm border dark:border-gray-800 border-gray-200 bg-white p-3 ',
                // 'fi-tabs flex max-w-full gap-x-1 overflow-x-auto',
                // 'fi-contained border-b border-gray-200 px-3 py-2.5 dark:border-white/10' => $contained,
                // 'mx-auto rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => ! $contained,
            ])
    }}
>
    {{ $slot }}
</div>
