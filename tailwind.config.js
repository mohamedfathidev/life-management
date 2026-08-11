import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    // Semantic status/category colors are chosen at runtime (enums), so the
    // JIT scanner can't see them in source — safelist the badge variants.
    safelist: [
        'bg-primary/15', 'text-primary',
        'bg-success/15', 'text-success',
        'bg-warning/15', 'text-warning',
        'bg-danger/15', 'text-danger',
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
                bg: { light: '#F7F7F5', dark: '#15191A' },
                surface: { light: '#FFFFFF', dark: '#1E2422' },
                primary: {
                    DEFAULT: '#3F7D7A', // muted teal
                    dark: '#5FA6A2',
                },
                secondary: {
                    DEFAULT: '#D8C9A3', // soft sand
                    dark: '#B9A97C',
                },
                success: { DEFAULT: '#7A9E7E', dark: '#8FB693' },
                warning: { DEFAULT: '#D9A25A', dark: '#E0B378' },
                danger: { DEFAULT: '#C77B7B', dark: '#D08F8F' },
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
