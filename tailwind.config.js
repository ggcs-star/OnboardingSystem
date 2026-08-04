import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Mapped to CSS variables in resources/css/theme.css —
                // change the palette there, not here.
                primary: {
                    DEFAULT: 'rgb(var(--color-primary) / <alpha-value>)',
                    dark: 'rgb(var(--color-primary-dark) / <alpha-value>)',
                    light: 'rgb(var(--color-primary-light) / <alpha-value>)',
                },
                secondary: {
                    DEFAULT: 'rgb(var(--color-secondary) / <alpha-value>)',
                    dark: 'rgb(var(--color-secondary-dark) / <alpha-value>)',
                    light: 'rgb(var(--color-secondary-light) / <alpha-value>)',
                },
                success: {
                    DEFAULT: 'rgb(var(--color-success) / <alpha-value>)',
                    light: 'rgb(var(--color-success-light) / <alpha-value>)',
                },
                danger: {
                    DEFAULT: 'rgb(var(--color-danger) / <alpha-value>)',
                    light: 'rgb(var(--color-danger-light) / <alpha-value>)',
                },
                warning: {
                    DEFAULT: 'rgb(var(--color-warning) / <alpha-value>)',
                    light: 'rgb(var(--color-warning-light) / <alpha-value>)',
                },
                surface: {
                    DEFAULT: 'rgb(var(--color-surface) / <alpha-value>)',
                    alt: 'rgb(var(--color-surface-alt) / <alpha-value>)',
                },
                'app-border': 'rgb(var(--color-border) / <alpha-value>)',
                sidebar: {
                    bg: 'rgb(var(--color-sidebar-bg) / <alpha-value>)',
                    'active-bg': 'rgb(var(--color-sidebar-active-bg) / <alpha-value>)',
                    'active-text': 'rgb(var(--color-sidebar-active-text) / <alpha-value>)',
                    text: 'rgb(var(--color-sidebar-text) / <alpha-value>)',
                },
                chart: {
                    1: 'rgb(var(--chart-cat-1) / <alpha-value>)',
                    2: 'rgb(var(--chart-cat-2) / <alpha-value>)',
                    3: 'rgb(var(--chart-cat-3) / <alpha-value>)',
                    4: 'rgb(var(--chart-cat-4) / <alpha-value>)',
                    5: 'rgb(var(--chart-cat-5) / <alpha-value>)',
                },
            },
        },
    },

    plugins: [forms, typography],
};
