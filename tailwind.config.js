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
            },
            colors: {
                'gbit-blue': {
                    DEFAULT: '#0f2a5c',
                    '50': '#f0f4fa',
                    '100': '#dce5f3',
                    '200': '#c0d0ea',
                    '300': '#9ab3dc',
                    '400': '#7090cc',
                    '500': '#5272bd',
                    '600': '#3f57a8',
                    '700': '#364989',
                    '800': '#0f2a5c', // Color base
                    '900': '#091d40',
                },
                'gbit-orange': {
                    DEFAULT: '#ff6600',
                    '50': '#fff7f0',
                    '100': '#ffead9',
                    '200': '#ffd0b0',
                    '300': '#ffb380',
                    '400': '#ff8c40',
                    '500': '#ff6600', // Color base
                    '600': '#e65c00',
                    '700': '#cc5200',
                    '800': '#a34200',
                    '900': '#7a3100',
                },
            },
        },
    },

    plugins: [forms],
};
