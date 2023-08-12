import * as motion from "motion";

document.addEventListener("alpine:init", () => {
    Alpine.magic("motion", (el, { Alpine }) => {
        return motion;
    });
});
