module.exports = {
  content: ["./app/**/*.php", "./resources/views/**/*.php"],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        bg: {
          950: "#050816",
          900: "#0a1020",
        },
        accent: {
          cyan: "#22d3ee",
          blue: "#3b82f6",
          violet: "#8b5cf6",
        },
      },
      boxShadow: {
        glow: "0 0 20px rgba(59,130,246,0.25)",
      },
    },
  },
  plugins: [],
};
