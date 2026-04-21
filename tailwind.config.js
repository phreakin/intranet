module.exports = {
  content: ["./app/**/*.php", "./resources/views/**/*.php"],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        bg: {
          950: "#050816",
          900: "#0a1020",
          800: "#111827",
          700: "#1f2937",
          600: "#374151",
          500: "#4b5563",
          400: "#6b7280",
          300: "#9ca3af",
          200: "#d1d5db",
          100: "#e5e7eb",
          50: "#f9fafb",
        },
        accent: {
          cyan: "#22d3ee",
          blue: "#3b82f6",
          violet: "#8b5cf6",
          green: "#10b981",
          yellow: "#fbbf24",
          orange: "#f97316",
          red: "#ef4444",
          pink: "#ec4899",
          purple: "#a855f7",
          teal: "#14b8a6",
          sky: "#0ea5e9",
          lime: "#84cc16",
          fuchsia: "#d946ef",
          indigo: "#6366f1",
         gray: "#6b7280",
        },
      },
      boxShadow: {
        glow: "0 0 20px rgba(59,130,246,0.25)",
      },
    },
  },
  plugins: [],
};
