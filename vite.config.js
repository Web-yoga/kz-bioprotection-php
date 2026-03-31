const { defineConfig } = require("vite");
const tailwindcss = require("@tailwindcss/vite").default;
const path = require("path");

module.exports = defineConfig(({ command }) => ({
  plugins: [tailwindcss()],
  base: command === "build" ? "/assets/" : "/",
  publicDir: false,
  build: {
    manifest: true,
    outDir: "public/assets",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, "resources/js/app.js"),
        "contact-form-textareas": path.resolve(
          __dirname,
          "resources/js/contact-form-textareas.js",
        ),
      },
      output: {
        /* Content hash in filenames so cache invalidates after each build; paths come from manifest.json */
        entryFileNames: "js/[name]-[hash].min.js",
        chunkFileNames: "js/[name]-[hash].js",
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith(".css")) {
            return "css/[name]-[hash].min.css";
          }
          return "[name]-[hash][extname]";
        },
      },
    },
  },
}));
