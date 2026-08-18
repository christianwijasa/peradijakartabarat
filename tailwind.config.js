import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
                serif: ['Lora', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                navy: {
                    DEFAULT: '#0d2a5c',
                    dark: '#0a2249',
                    light: '#123a75',
                },
                primary: {
                    DEFAULT: '#164a94',
                    light: '#4d7cc7',
                },
                surface: '#eceef2',
                tag: {
                    ok: { bg: '#eaf1ec', fg: '#2e6b4f' },
                    wait: { bg: '#faf1e0', fg: '#8a5a12' },
                    info: { bg: '#e9eef6', fg: '#2a4d78' },
                    bad: { bg: '#f9ebe7', fg: '#93321f' },
                    mute: { bg: '#f2f3f6', fg: '#5b5d68' },
                },
            },
        },
    },

    plugins: [forms],
};
