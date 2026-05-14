import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Manrope', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    xlight: '#EFF6FF',
                    light: '#DBEAFE',
                    DEFAULT: '#2563EB',
                    dark: '#1D4ED8',
                    deep: '#1E3A8A',
                },
                base: {
                    page: '#F0F4FF',
                    card: '#FFFFFF',
                    input: '#F8FAFC',
                },
                content: {
                    primary: '#1E293B',
                    secondary: '#64748B',
                    tertiary: '#94A3B8',
                },
                border: '#E2E8F0',
                status: {
                    success: '#22C55E',
                    warning: '#F59E0B',
                    danger: '#EF4444',
                    info: '#38BDF8',
                }
            },
            boxShadow: {
                'card-sm': '0 2px 8px rgba(15, 23, 42, 0.06)',
                'card': '0 4px 16px rgba(37, 99, 235, 0.08)',
                'card-hover': '0 8px 32px rgba(37, 99, 235, 0.12)',
                'modal': '0 16px 48px rgba(15, 23, 42, 0.16)',
            },
            borderRadius: {
                'sm': '8px',
                'md': '12px',
                'lg': '16px',
                'xl': '24px',
            }
        },
    },

    plugins: [forms],
};
