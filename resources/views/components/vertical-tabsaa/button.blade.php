<button x-bind="tabbutton" type="button" 
    :class="twMerge(
        'border border-transparent border-r-0 shrink-0 p-3 text-sm font-medium text-left rounded-l-lg rounded-r-lg sm:rounded-r-none focus:ring-2 focus:ring-primary-600 focus:ring-inset',
        isSelected($el.id) ? 'text-primary-600 bg-white dark:text-white dark:bg-primary-600 border-gray-200' : 'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400'
    )">
    {{ $slot }}
</button>
