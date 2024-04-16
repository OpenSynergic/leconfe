const colors = require('tailwindcss/colors')

import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/frontend/**/*.blade.php',
    ],
    theme: {
        extend: {
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
                    // primary: '#BA2823',
                    primary: '#38BDF8',
                    'primary-content': '#ffffff',
                    secondary: '#4a4a5b',
                    'base-100': '#F1F6FA',
                    '--rounded-box': '0px',
                    "--rounded-btn": "0.25rem",
                    '--btn-text-case': 'none',
                    // '--padding-card': '1rem',
                },
            },
        ],
    },
}
