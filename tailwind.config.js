/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: "tw-",
  content: [
    "./**/*.php",
    "./**/*.js",
    "./**/*.jsx",
    "./**/*.ts",
    "./**/*.tsx",
    "./**/*.html",
    "!./vendor/**/*",
    "!./node_modules/**/*",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

