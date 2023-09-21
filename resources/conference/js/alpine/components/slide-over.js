document.addEventListener("alpine:init", () => {
    Alpine.directive("slide-over", (el, directive) => {
        if (!directive.value) handleRoot(el, Alpine);
    })

    function handleRoot(el, Alpine) {
        Alpine.bind(el, () => {
            return {
                "x-id"() {
                    return ["alpine-slide-over"];
                },
                ":id"() {
                    return this.$id("alpine-slide-over");
                },
                "x-data"() {
                    return {
                        slideOverOpen: false,
                        toggleSlideOver() {
                            this.slideOverOpen = !this.slideOverOpen;
                        },
                        closeSlideOver() {
                            this.slideOverOpen = false;
                        },
                        openSlideOver() {
                            this.slideOverOpen = true;
                        },
                        init() {},
                    };
                },
            };
        });
    }

});