import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php", "./storage/framework/views/*.php", "./resources/views/**/*.blade.php", "*.{js,ts,jsx,tsx,mdx}"],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'custom-pink-50': '#FFF0F5', // Light pink for background gradient start
                'custom-purple-50': '#F8F0FF', // Light purple for background gradient end
                'primary-pink': '#EC4899', // A vibrant pink for buttons/accents
                'primary-orange': '#F97316', // A vibrant orange for buttons/accents
                'accent-yellow': '#FACC15', // Yellow for total votes/top miss
                'text-gray-900': '#1F2937',
                'text-gray-800': '#374151',
                'text-gray-700': '#4B5563',
                'text-gray-600': '#6B7280',
                'text-pink-600': '#DB2777', // For vote counts
                'bg-pink-100': '#FCE7F6', // Light pink background for vote count badge
                'text-pink-700': '#BE185D', // Darker pink for vote count badge text
                'bg-pink-200': '#FBCFE8', // For vote badge on vote page
                'text-pink-800': '#9D174D', // For vote badge on vote page text
                'bg-yellow-400': '#FACC15', // For total votes badge
                'text-yellow-900': '#78350F', // For total votes badge text
                'gradient-pink-light': '#f0ddbbff',
            },
        },
    },

    plugins: [forms, require('@tailwindcss/aspect-ratio')],
};
