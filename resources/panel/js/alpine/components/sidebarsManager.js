import Sortable from 'sortablejs'

document.addEventListener('alpine:init', () => {
    Alpine.data('sidebarsManager', (items) => ({
        items,
        sortable: null,
        saving: false,
        async init() {
            await this.$nextTick()

            // console.log(this.$refs.sortable)
            this.sortable = new Sortable(this.$refs.sortable, {
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.55,
                draggable: '[data-sortable-item]',
                handle: '[data-sortable-handle]',
            })
        },
        get sortedItems() {
            return this.sortable.toArray().filter((id) => {
                let item = this.items.find((item) => item.id === id);

                return item.isActive;
            });
        },
        save(){
            return this.$wire.save(this.sortedItems);
        }
    }))
})
