import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                heading: ['Cormorant Garamond', 'Playfair Display', 'serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#C8860A',
                    hover: '#A36D08',
                    light: '#FEF3C7',
                    glow: 'rgba(200,134,10,0.2)',
                },
                'brown-dark': '#1E1008',
                'brown-mid': '#5C3D2E',
                'brown-light': '#A0765A',
                cream: {
                    DEFAULT: '#FDFAF4',
                    warm: '#FFF3E0',
                    border: '#EDE0CE',
                    dark: '#E8D5BC',
                },
                'text-primary': '#1E1008',
                'text-secondary': '#6B5544',
                'text-muted': '#A08070',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            boxShadow: {
                'gold': '0 6px 24px rgba(200,134,10,0.25)',
                'gold-lg': '0 10px 40px rgba(200,134,10,0.35)',
                'sm': '0 2px 8px rgba(30,16,8,0.08)',
                'md': '0 6px 20px rgba(30,16,8,0.12)',
                'lg': '0 16px 40px rgba(30,16,8,0.16)',
            },
            backgroundImage: {
                'hero-gradient': 'linear-gradient(135deg, #FDFAF4 0%, #FFF3E0 100%)',
                'gold-gradient': 'linear-gradient(135deg, #C8860A 0%, #A36D08 100%)',
            },
        },
    },
    plugins: [forms],
};
