/** @type {import('tailwindcss').Config} */
export default {
  content: ["./resources/index.html", "./resources/js/**/*.{vue,ts}"],
  theme: {
    extend: {
      colors: {
        brand: {
          50: "#eef4ff",
          100: "#d9e6ff",
          500: "#3b5fe0",
          600: "#2d4ac2",
          700: "#22399a",
        },
      },
    },
  },
  plugins: [],
};
