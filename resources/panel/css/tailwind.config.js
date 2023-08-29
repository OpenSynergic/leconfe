import preset from "../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Panel/**/*.php",
        "./app/Administration/**/*.php",
        "./resources/views/vendor/**/*.blade.php",
        "./resources/views/panel/**/*.blade.php",
        "./resources/views/administration/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php",
    ],
    plugins: [require("tailwindcss-animate")],
};
