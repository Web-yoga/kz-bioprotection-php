/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/templates/**/*.php",
    "./resources/js/**/*.js",
  ],
  theme: {
    container: {
      center: true,
      padding: "1rem",
      screens: {
        "2xl": "1140px",
      },
    },
    extend: {
      borderRadius: {
        "16": "16px",
      },
    },
  },
};
