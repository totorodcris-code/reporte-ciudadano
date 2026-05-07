import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                'xs': '475px',
                '3xl': '1600px',
            },
            colors: {
                primary: {
                    50: '#F0FCFC',
                    100: '#D0F5F5',
                    200: '#A3EFEF',
                    300: '#97FEED',
                    400: '#35A29F',
                    500: '#0B666A',
                    600: '#0A5859',
                    700: '#084548',
                    800: '#073538',
                    900: '#071952',
                },
                secondary: {
                    50: '#F0F4F8',
                    100: '#DBE4F0',
                    200: '#BDD0E2',
                    300: '#94B8D0',
                    400: '#6B9BB8',
                    500: '#4A7B9D',
                    600: '#3D6280',
                    700: '#324D66',
                    800: '#2B4052',
                    900: '#243546',
                },
                contrast: {
                    900: '#071952',
                    800: '#0B666A',
                    700: '#35A29F',
                    600: '#97FEED',
                    highlight: '#35A29F',
                    success: '#10B981',
                    warning: '#F59E0B',
                    danger: '#EF4444',
                },
            },
            fontSize: {
                'accessible-sm': ['1.125rem', { lineHeight: '1.75' }],
                'accessible-base': ['1.125rem', { lineHeight: '1.75' }],
                'accessible-lg': ['1.25rem', { lineHeight: '1.75' }],
                'accessible-xl': ['1.5rem', { lineHeight: '1.75' }],
                'accessible-2xl': ['1.875rem', { lineHeight: '1.75' }],
            },
            minHeight: {
                'touch': '48px',
                'touch-lg': '56px',
                'mobile-screen': '100vh',
                'mobile-screen-safe': '100dvh',
            },
            minWidth: {
                'touch': '48px',
                'mobile-full': '100vw',
            },
            maxWidth: {
                'mobile-full': '100vw',
                'mobile-wrapper': 'calc(100vw - 2rem)',
                'mobile-content': 'calc(100vw - 4rem)',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
                '144': '36rem',
            },
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
            },
        },
    },

    plugins: [forms],
};
