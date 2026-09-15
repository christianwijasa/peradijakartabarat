import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    safelist: ['app-check-wait'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                // Headings & stats: same family, heavier weight (modern sans, no traditional serif)
                serif: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ink: {
                    DEFAULT: '#191a20',
                    secondary: '#4a4c57',
                },
                navy: {
                    DEFAULT: '#0d2a5c',
                    dark: '#071d3f',
                    light: '#123a75',
                },
                primary: {
                    DEFAULT: '#164a94',
                    light: '#4d7cc7',
                },
                accent: {
                    DEFAULT: '#c9a227',
                    muted: '#e8d9a8',
                },
                surface: '#eef0f4',
                muted: {
                    DEFAULT: '#f4f5f8',
                    foreground: '#7a7d8b',
                },
                line: '#e4e6ec',
                sidebar: {
                    muted: '#aec1e4',
                    dim: '#7a93c2',
                },
                tag: {
                    ok: { bg: '#eaf1ec', fg: '#2e6b4f' },
                    wait: { bg: '#faf1e0', fg: '#8a5a12' },
                    info: { bg: '#e9eef6', fg: '#2a4d78' },
                    bad: { bg: '#f9ebe7', fg: '#93321f' },
                    mute: { bg: '#f2f3f6', fg: '#5b5d68' },
                },
            },
            boxShadow: {
                card: '0 1px 2px rgba(13, 42, 92, 0.04), 0 8px 24px rgba(13, 42, 92, 0.06)',
                'card-hover': '0 4px 12px rgba(13, 42, 92, 0.08), 0 16px 40px rgba(13, 42, 92, 0.08)',
                nav: '0 1px 3px rgba(13, 42, 92, 0.12)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.25rem',
            },
        },
    },

    plugins: [forms],
};
