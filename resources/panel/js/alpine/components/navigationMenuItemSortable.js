import Sortable from 'sortablejs'

document.addEventListener('alpine:init', () => {
    Alpine.data('navigationMenuItemSortable', (options) => ({
        parentId: options?.parentId,
        sortable: null,
        init() {
            this.sortable = new Sortable(this.$el, {
                // group: 'group',
                group: options?.group,
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.55,
                draggable: '[data-sortable-item]',
                handle: '[data-sortable-handle]',
                onSort: (evt) => {
                    this.sorted()
                },
            })
        },
        sorted() {
            if(this.sortable.toArray().length === 0) {
                return;
            }
            this.$wire.sortNavigationMenuItems(this.sortable.toArray(), this.parentId);
        },
    }))
})
