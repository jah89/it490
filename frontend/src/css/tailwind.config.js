/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./../lib/components/*.php',
    './../**/includes/*.php',
    './../**/*.php'
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
//./tailwindcss -i input.css -o output.css --minify(to create output css)
