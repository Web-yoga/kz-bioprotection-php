const { defineConfig } = require("vite");
const tailwindcss = require("@tailwindcss/vite").default;
const path = require("path");

module.exports = defineConfig({
  plugins: [tailwindcss()],
  build: {
    manifest: true,
    outDir: "public/assets",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, "resources/js/app.js"),
      },
      output: {
        entryFileNames: "js/[name].min.js",
        chunkFileNames: "js/[name]-[hash].js",
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith(".css")) {
            return "css/[name].min.css";
          }
          return "assets/[name]-[hash][extname]";
        },
      },
    },
  },
});
