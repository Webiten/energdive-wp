import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import path from "path";

export default defineConfig({
  plugins: [react(), tailwindcss()],

  // 🔑 IMPORTANT for WordPress plugin
  base: "",

  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },

  server: {
    proxy: {
      "/wp-json": {
        target: "https://stage.energdive.com",
        changeOrigin: true,
        secure: false,
      },
    },
  },

  build: {
    outDir: "dist",
    emptyOutDir: true,

    rollupOptions: {
      // 🔥 THIS WAS MISSING
      input: path.resolve(__dirname, "src/main.tsx"),
    },
  },
});
