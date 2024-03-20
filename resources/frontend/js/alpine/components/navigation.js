document.addEventListener("alpine:init", () => {
    Alpine.directive("navigation", (el, directive) => {
        if (!directive.value) handleRoot(el, Alpine);
        else if (directive.value === "trigger")
            handleTrigger(el, Alpine, directive.expression);
        else if (directive.value === "dropdown") handleDropdown(el, Alpine);
        else if (directive.value === "dropdown-content")
            handleDropdownContent(el, Alpine, directive.expression);
    }).before("bind");

    function handleRoot(el, Alpine) {
        Alpine.bind(el, () => {
            return {
                "x-id"() {
                    return ["alpine-navigation"];
                },
                ":id"() {
                    return this.$id("alpine-navigation");
                },
                "x-data"() {
                    return {
                        navigationMenuOpen: false,
                        navigationMenu: "",
                        navigationMenuCloseDelay: 200,
                        navigationMenuCloseTimeout: null,
                        navigationMenuLeave() {
                            let that = this;
                            this.navigationMenuCloseTimeout = setTimeout(() => {
                                that.navigationMenuClose();
                            }, this.navigationMenuCloseDelay);
                        },
                        navigationMenuReposition(navElement) {
                            this.navigationMenuClearCloseTimeout();
                            this.$refs.navigationDropdown.style.left =
                                navElement.offsetLeft + "px";
                            this.$refs.navigationDropdown.style.marginLeft =
                                navElement.offsetWidth / 2 + "px";
                        },
                        isNavigationMenuOpen(key){
                            return this.navigationMenuOpen == true && this.navigationMenu == key;
                        },
                        navigationMenuClearCloseTimeout() {
                            clearTimeout(this.navigationMenuCloseTimeout);
                        },
                        navigationMenuClose() {
                            this.navigationMenuOpen = false;
                            this.navigationMenu = "";
                        },
                        init() {},
                    };
                },
            };
        });
    }

    function handleTrigger(el, Alpine, key) {
        Alpine.bind(el, () => {
            return {
                ":id"() {
                    return this.$id("alpine-navigation-trigger");
                },
                ["@mouseenter"]() {
                    this.navigationMenuOpen = true;
                    this.navigationMenuReposition(this.$el);
                    this.navigationMenu = key;
                },
                // ["@mouseleave"]() {
                //     this.navigationMenuLeave();
                // },
            };
        });
    }

    function handleDropdown(el, Alpine) {
        Alpine.bind(el, () => {
            return {
                "x-ref": "navigationDropdown",
                ["x-show"]() {
                    return this.navigationMenuOpen;
                },
                ["@mouseover"]() {
                    this.navigationMenuClearCloseTimeout();
                },
                // ["@mouseleave"]() {
                //     this.navigationMenuLeave();
                // },
            };
        });
    }

    function handleDropdownContent(el, Alpine, key) {
        Alpine.bind(el, () => {
            return {
                ["x-show"]() {
                    return this.navigationMenu == key;
                },
            };
        });
    }
});
