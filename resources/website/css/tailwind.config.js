const colors = require('tailwindcss/colors')

import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/conference/**/*.blade.php',
        './resources/views/website/**/*.blade.php',
        './resources/views/examples/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary : '#38BDF8',
                secondary: '#4a4a5b',
            },
            fontFamily: {
                sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
            },
            fontSize : {
                '2xs' : '.65rem',
            },
            typography: (theme) => ({
                DEFAULT: {
                  css: {
                    a: {
                        'text-decoration': 'none',
                    }

                    // ...
                  },
                },
              }),
        },
    },
    darkMode: 'class',
    plugins: [
        require('daisyui'),
        require('@tailwindcss/typography'),
        require("tailwindcss-animate"),
    ],
    daisyui: {
        themes: [
            {
                light: {
                    ...require('daisyui/src/theming/themes')[
                        '[data-theme=winter]'
                    ],
                    primary: '#38BDF8',
                    secondary: '#4a4a5b',
                    'base-100': '#F1F6FA',
                    '--rounded-box': '0.5rem',
                    '--btn-text-case': 'none',
                    // '--padding-card': '1rem',
                },
            },
        ],
        // true: all themes | false: only light + dark | array: specific themes like this ["light", "dark", "cupcake"]
        base: true, // applies background color and foreground color for root element by default
        styled: true, // include daisyUI colors and design decisions for all components
        utils: true, // adds responsive and modifier utility classes
    },
}
