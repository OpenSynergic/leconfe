import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/panel/css/panel.css",
                "resources/panel/js/panel.js",
                "resources/website/css/website.css",
                "resources/website/js/website.js",
            ],
            refresh: true,
        }),
    ],
});
