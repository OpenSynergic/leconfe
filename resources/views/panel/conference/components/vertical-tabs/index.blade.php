@props([
    'contained' => false,
    'label' => null,
    'isSticky' => true,
    'verticalSpace' => null,
])
<div
    {{
        $attributes
            ->merge([
                'aria-label' => $label,
                'role' => 'tablist',
                'class' => $verticalSpace ? $verticalSpace : null,
            ])
            ->class([
                'flex flex-row xl:flex-col justify-center mx-auto xl:min-w-72 dark:bg-gray-900 rounded-xl shadow-sm bg-white p-3 self-start',
                'sticky top-24 z-2' => $isSticky,
            ])
    }}
>
    {{ $slot }}
</div>
