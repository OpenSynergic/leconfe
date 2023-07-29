document.addEventListener("alpine:init", () => {
    Alpine.data("tabcomponent", () => ({
        selectedId: null,
        init() {
            this.$nextTick(() => this.select(this.$id("alpinetabs", 1)));
        },
        select(id) {
            this.selectedId = id;
        },
        isSelected(id) {
            return this.selectedId === id;
        },
        whichChild(el, parent) {
            return Array.from(parent.children).indexOf(el) + 1;
        },
        tablist: {
            ["@keydown.right.prevent.stop"]() {
                this.$focus.wrap().next();
            },
            ["@keydown.home.prevent.stop"]() {
                this.$focus.first();
            },
            ["@keydown.page-up.prevent.stop"]() {
                this.$focus.first();
            },
            ["@keydown.left.prevent.stop"]() {
                this.$focus.wrap().prev();
            },
            ["@keydown.end.prevent.stop"]() {
                this.$focus.last();
            },
            ["@keydown.page-down.prevent.stop"]() {
                this.$focus.last();
            },
        },
        tabbutton: {
            [":id"]() {
                return this.$id(
                    "alpinetabs",
                    this.whichChild(this.$el, this.$el.parentElement)
                );
            },
            ["@click"]() {
                this.select(this.$el.id);
            },
            ["@focus"]() {
                this.select(this.$el.id);
            },
            [":aria-selected"]() {
                return this.isSelected(this.$el.id);
            },
        },
        tabcontent: {
            ["x-show"]() {
                return this.isSelected(
                    this.$id(
                        "alpinetabs",
                        this.whichChild(this.$el, this.$el.parentElement)
                    )
                );
            },
            [":aria-labelledby"]() {
                return this.$id(
                    "alpinetabs",
                    this.whichChild(this.$el, this.$el.parentElement)
                );
            },
        },
    }));
});
