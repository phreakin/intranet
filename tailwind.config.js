/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/**/*.php',
    './public/assets/js/**/*.js',
    './resources/views/**/*.php',
    './app/**/*.php',
    './*.php',
  ],
  theme: {
    extend: {
      colors: {
        intranet: {
          bg: '#04070d',
          elevated: '#09111f',
          panel: '#0b1426',
          cyan: '#5ee6ff',
          blue: '#57a7ff',
          violet: '#8978ff',
          teal: '#4ce0c0',
          success: '#4fd887',
          warning: '#ffcb6b',
          danger: '#ff6f91',
          text: '#edf4ff',
          muted: '#9caecc',
          dim: '#6f7f9f',
        },
      },
      boxShadow: {
        glass: '0 16px 40px rgba(0, 0, 0, 0.28)',
        glow: '0 0 24px rgba(87, 167, 255, 0.2)',
      },
      borderRadius: {
        glass: '18px',
      },
      backdropBlur: {
        glass: '18px',
      },
      backgroundImage: {
        'intel-shell':
          'radial-gradient(circle at 12% 12%, rgba(94, 230, 255, 0.11), transparent 0 26%), radial-gradient(circle at 82% 10%, rgba(137, 120, 255, 0.14), transparent 0 24%), radial-gradient(circle at 48% 100%, rgba(87, 167, 255, 0.1), transparent 0 32%), linear-gradient(180deg, #03060c 0%, #07101c 48%, #04070d 100%)',
        'intel-panel':
          'linear-gradient(180deg, rgba(11, 20, 38, 0.9), rgba(7, 13, 26, 0.82))',
      },
      fontFamily: {
        sans: ['Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif'],
      },
    },
  },
  plugins: [],
};
