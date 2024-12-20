/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/**/*.{html,js,ts,jsx,tsx,php}',
    './templates/**/*.twig',
  ],
  theme: {
    extend: {
      colors: {
        customBlue: '#0a1128', // Bleu personnalisé
      },
    },
  },
  plugins: [],
}

