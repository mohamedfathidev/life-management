import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    // Semantic status/category colors are chosen at runtime (enums), so the
    // JIT scanner can't see them in source — safelist the badge variants.
    safelist: [
        'bg-primary/15', 'text-primary', 'bg-primary',
        'bg-success/15', 'text-success', 'bg-success',
        'bg-warning/15', 'text-warning', 'bg-warning',
        'bg-danger/15', 'text-danger', 'bg-danger',
        'bg-ink-soft', 'bg-ink-soft/15', 'text-ink-soft',

        // Recovery dreams cards cycle through this palette by index (§ dreams.blade.php)
        ...['primary', 'secondary', 'success'].flatMap((c) => [
            `hover:border-${c}/30`, `dark:hover:border-${c}-dark/30`,
            `bg-${c}/10`, `dark:bg-${c}-dark/10`,
            `bg-${c}/15`, `dark:bg-${c}-dark/20`,
            `text-${c}`, `dark:text-${c}-dark`,
        ]),
    ],

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', 'Tajawal', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Quiet & clear palette (§9) — light + dark handled via `dark:` variants
                // Now using CSS custom properties for dynamic color schemes
                bg: { light: '#F7F7F5', dark: '#15191A' },
                surface: { light: '#FFFFFF', dark: '#1E2422' },
                primary: {
                    DEFAULT: 'rgb(var(--color-primary) / <alpha-value>)',
                    dark: 'rgb(var(--color-primary-dark) / <alpha-value>)',
                },
                secondary: {
                    DEFAULT: 'rgb(var(--color-secondary) / <alpha-value>)',
                    dark: 'rgb(var(--color-secondary-dark) / <alpha-value>)',
                },
                success: {
                    DEFAULT: 'rgb(var(--color-success) / <alpha-value>)',
                    dark: 'rgb(var(--color-success-dark) / <alpha-value>)',
                },
                warning: {
                    DEFAULT: 'rgb(var(--color-warning) / <alpha-value>)',
                    dark: 'rgb(var(--color-warning-dark) / <alpha-value>)',
                },
                danger: {
                    DEFAULT: 'rgb(var(--color-danger) / <alpha-value>)',
                    dark: 'rgb(var(--color-danger-dark) / <alpha-value>)',
                },
                ink: {
                    DEFAULT: '#2B2B2B', // text primary (light)
                    soft: '#767676',    // text secondary (light)
                    dark: '#EDEDEA',    // text primary (dark)
                    'dark-soft': '#A3A3A0',
                },
            },
        },
    },

    plugins: [forms],
};
