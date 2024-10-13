/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["../../src/**/includes/*.php",
    "../../src/*.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
//./tailwindcss -i input.css -o output.css --minify(to create output css)
