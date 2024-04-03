document.addEventListener('alpine:init', () => {
    Alpine.data('carousel', () => ({
        index: 3,
        toRight() {
            this.to((current, offset) => current + offset * this.index)
        },
        toLeft() {
            this.to((current, offset) => current - offset * this.index)
        },
        to(strategy) {
            let slider = this.$refs.slider
            let current = slider.scrollLeft
            let offset = slider.firstElementChild.getBoundingClientRect().width
            slider.scrollTo({
                left: strategy(current, offset),
                behavior: 'smooth',
            })
        },
        focusableWhenVisible: {
            'x-intersect:enter'() {
                this.$el.removeAttribute('tabindex')
            },
            'x-intersect:leave'() {
                this.$el.setAttribute('tabindex', '-1')
            },
        },
    }))

    Alpine.bind('carousel', () => ({
        ['x-on:keydown.right']() {
            this.toRight()
        },
        ['x-on:keydown.left']() {
            this.toLeft()
        },
        ['tabindex']: '0',
        ['role']: 'region',
        ['aria-labelledby']: 'carousel-label',
    }))
})
