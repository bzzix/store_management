import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', 'Cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: 'var(--color-primary-50)',
                    100: 'var(--color-primary-100)',
                    500: 'var(--color-primary-500)',
                    600: 'var(--color-primary-600)',
                    700: 'var(--color-primary-700)',
                },
                secondary: {
                    50: 'var(--color-secondary-50)',
                    500: 'var(--color-secondary-500)',
                    600: 'var(--color-secondary-600)',
                },
                surface: {
                    50: 'var(--color-surface-50)',
                    100: 'var(--color-surface-100)',
                    200: 'var(--color-surface-200)',
                    300: 'var(--color-surface-300)',
                    400: 'var(--color-surface-400)',
                    500: 'var(--color-surface-500)',
                    600: 'var(--color-surface-600)',
                    700: 'var(--color-surface-700)',
                    800: 'var(--color-surface-800)',
                    900: 'var(--color-surface-900)',
                },
            },
        },
    },

    plugins: [forms, typography],
};
