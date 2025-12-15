import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/livewire/livewire/src/Features/SupportPagination/views/*.blade.php',
    ],

    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Roboto', ...defaultTheme.fontFamily.sans],
                serif: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': '#001d50',      // Hijau Pinus yang dalam dan elegan
                'secondary': {
                    light: '#90bd2d',  // Off-white / Krem untuk latar belakang
                    dark: '#174e2b',   // Charcoal yang lebih lembut
                },
                'accent': '#f5bf1a',
                'green': {
                    default: '#E2F0BD',
                    light: '#90bd2d',
                    dark: '#174e2b',
                },

                'greener': '#03a27aff',
                'soft': '#f8f7f0',
                'light': '#e9ecef',
                'green-bright': '#DAFD43',


            },
        },
    },

    safelist: [
        'text-emerald-400',
        'text-emerald-300',
        'bg-emerald-600',
        'bg-emerald-700',
        'hover:bg-emerald-700',
        'border-emerald-500',
        // Tambahkan kelas lain yang kamu tahu akan kamu gunakan di database
        // Kamu juga bisa menggunakan pola regex, misalnya:
        // { pattern: /bg-(red|green|blue)-(100|200|300)/ }
    ],

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
};
