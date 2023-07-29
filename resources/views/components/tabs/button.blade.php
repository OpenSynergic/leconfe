<button
    x-bind="tabbutton" 
    :class="isSelected($el.id) ? 'text-primary-600 shadow bg-white dark:text-white dark:bg-primary-600' : 'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400'"
    role="tab"
    type="button"
    {{ $attributes->merge(['class' => 'flex items-center h-8 px-5 font-medium rounded-lg whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-inset']) }}
    >
    {{ $slot }}
</button>