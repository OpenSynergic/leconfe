import Sortable from 'sortablejs'

document.addEventListener('alpine:init', () => {
    Alpine.data('blockSortableContainer', ({ statePath }) => ({
        statePath,
        sortable: null,
        init() {
            this.sortable = new Sortable(this.$el, {
                group: 'shared',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.55,
                onSort: (evt) => {
                    this.update()
                },
            })
        },
        update() {
            this.$wire.updateBlocks(this.statePath, this.sortable.toArray())
        },
    }))
})
