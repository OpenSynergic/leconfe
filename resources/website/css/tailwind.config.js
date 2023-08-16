const colors = require('tailwindcss/colors')

import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
    content: ['./resources/views/website/**/*.blade.php'],
    theme: {
        extend: {
            fontFamily: {
                sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
            },
            typography: (theme) => ({
                'sm': {
                  css: {
                    p: {
                        'margin-top': '0.5rem',
                      },
                  },
                },
              }),
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('daisyui'),
        require("tailwindcss-animate"),
    ],
    daisyui: {
        themes: [
            {
                light: {
                    ...require('daisyui/src/theming/themes')[
                        '[data-theme=winter]'
                    ],
                    // primary: colors.sky[600],
                    'base-100': '#F1F6FA',
                    '--rounded-box': '0.5rem',
                    '--btn-text-case': 'none',
                    // '--padding-card': '1rem',
                },
            },
        ], // true: all themes | false: only light + dark | array: specific themes like this ["light", "dark", "cupcake"]
        base: true, // applies background color and foreground color for root element by default
        styled: true, // include daisyUI colors and design decisions for all components
        utils: true, // adds responsive and modifier utility classes
        rtl: true, // rotate style direction from left-to-right to right-to-left. You also need to add dir="rtl" to your html tag and install `tailwindcss-flip` plugin for Tailwind CSS.
    },
}
