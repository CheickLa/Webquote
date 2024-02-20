/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'webquote': {
          '50': '#f1f7fe',
          '100': '#e2edfc',
          '200': '#bfd9f8',
          '300': '#87bbf2',
          '400': '#4899e8',
          '500': '#1f78d1',
          '600': '#1260b7',
          '700': '#104c94',
          '800': '#11427b',
          '900': '#143866',
          '950': '#0d2444',
        },
        'wqdark': {
          'main': '#171717',
          'secondary': '#252525',
          'background': '#1E1E1E',
        },
      },
      borderRadius: {
        '6': '0.375rem', // 6px
        '10': '0.625rem', // 10px
      },
      fontFamily: {
        'webquote': ['Lobster', 'sans-serif'],
        'title': ['"Plus Jakarta Sans"', 'sans-serif'],
      },
      maxWidth: {
        '4/5': '80%',
      },
    },
  },
  plugins: [],
}
