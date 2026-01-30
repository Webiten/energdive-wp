export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  // This ensures your Tailwind styles take precedence over WP/Elementor
  important: '#energ-members-root', 
  corePlugins: {
    // If transparency persists, we might need to disable preflight, 
    // but let's try the 'important' selector first.
    preflight: true, 
  },
  theme: {
    extend: {},
  },
  plugins: [],
};