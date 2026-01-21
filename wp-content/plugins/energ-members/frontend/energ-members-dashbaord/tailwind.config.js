/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}"
  ],
  corePlugins: {
    preflight: false, // ⛔ STOP Tailwind reset (WP ke saath clash)
  },
  theme: {
    extend: {},
  },
  plugins: [],
};
