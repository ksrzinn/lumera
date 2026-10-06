import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                script: ['"Dancing Script"', 'cursive'],
            },
            colors: {
                rosa: {
                    50: '#fdf5f6',
                    100: '#fce8ec',
                    200: '#f9d5dc',
                    300: '#f4b6c3',
                    400: '#ec8fa5',
                    500: '#e0708e',
                    600: '#cc4f72',
                    700: '#b03a5c',
                    800: '#8f314d',
                    900: '#772c43',
                },
            },
            boxShadow: {
                card: '0 2px 10px rgba(204, 79, 114, 0.10)',
            },
        },
    },

    plugins: [forms],
};
