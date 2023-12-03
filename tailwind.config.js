/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'wq-blue': {
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

      }
    },
  },
  plugins: [],
}