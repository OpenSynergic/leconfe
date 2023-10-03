@props([
    'contained' => false,
    'label' => null,
    'isSticky' => true
])
<div
    {{
        $attributes
            ->merge([
                'aria-label' => $label,
                'role' => 'tablist',
            ])
            ->class([
                'flex flex-row xl:flex-col justify-center mx-auto xl:w-72 dark:bg-gray-900 rounded-xl shadow-sm border dark:border-gray-800 border-gray-200 bg-white p-3 self-start',
                'sticky top-24 z-10' => $isSticky,
            ])
    }}
>
    {{ $slot }}
</div>
