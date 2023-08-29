import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/panel/css/panel.css",
                "resources/panel/js/panel.js",
                "resources/conference/css/conference.css",
                "resources/conference/js/conference.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources",
        },
    },
});
