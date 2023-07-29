/** @type {import('tailwindcss').Config} */

const colors = require("tailwindcss/colors");

module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./vendor/awcodes/filament-tiptap-editor/resources/views/**/*.blade.php",
    ],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                primary: colors.sky,
                danger: colors.rose,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
};
