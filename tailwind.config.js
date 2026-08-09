/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,ts,jsx,tsx}',
  ],
  corePlugins: {
    preflight: false,
  },
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', 'sans-serif'],
      },
      colors: {
        brand: {
          900: '#143b59',
          dark: '#0d2a40',
          accent: '#d39a2c',
          surface: '#f5f7f8',
          gray: '#dfe5e8',
          red: '#b63b35',
          muted: '#7a8790',
        },
      },
      boxShadow: {
        glass: '0 8px 32px 0 rgba(20, 59, 89, 0.08)',
        float: '0 20px 40px -10px rgba(20, 59, 89, 0.15)',
        soft: '0 8px 24px rgba(20, 59, 89, 0.08)',
        'accent-glow': '0 10px 30px -10px rgba(211, 154, 44, 0.4)',
      },
      screens: {
        xs: '475px',
      },
    },
  },
  plugins: [],
};
