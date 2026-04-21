/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.php",
    "./public/**/*.php",
    "./resources/views/**/*.php",
    "./resources/views/**/*.html",
    "./resources/views/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        bg: {
          950: "#070b12",
          900: "#0b1220",
          850: "#0f1726",
          800: "#111827"
        },
        panel: {
          DEFAULT: "rgba(15, 23, 38, 0.72)",
          strong: "rgba(11, 18, 32, 0.88)",
          soft: "rgba(17, 24, 39, 0.58)"
        },
        line: {
          subtle: "rgba(148, 163, 184, 0.12)",
          DEFAULT: "rgba(148, 163, 184, 0.18)",
          strong: "rgba(148, 163, 184, 0.28)"
        },
        text: {
          DEFAULT: "#e5eefb",
          soft: "#b6c4d9",
          dim: "#7f8ea3",
          muted: "#94a3b8"
        },
        brand: {
          cyan: "#22d3ee",
          blue: "#60a5fa",
          violet: "#8b5cf6",
          indigo: "#6366f1"
        },
        status: {
          success: "#10b981",
          warning: "#f59e0b",
          danger: "#ef4444",
          info: "#38bdf8"
        },
        chip: {
          category: "#2563eb",
          tag: "#14b8a6",
          hot: "#f97316",
          trending: "#8b5cf6",
          new: "#22c55e",
          reported: "#ef4444"
        }
      },
      fontFamily: {
        sans: [
          "Inter",
          "system-ui",
          "-apple-system",
          "BlinkMacSystemFont",
          "\"Segoe UI\"",
          "sans-serif"
        ],
        display: [
          "Space Grotesk",
          "Inter",
          "system-ui",
          "sans-serif"
        ],
        mono: [
          "JetBrains Mono",
          "Fira Code",
          "ui-monospace",
          "SFMono-Regular",
          "monospace"
        ]
      },
      boxShadow: {
        glow: "0 0 0 1px rgba(96,165,250,.12), 0 10px 30px rgba(2,6,23,.45)",
        "glow-cyan": "0 0 0 1px rgba(34,211,238,.18), 0 0 22px rgba(34,211,238,.14)",
        "glow-blue": "0 0 0 1px rgba(96,165,250,.18), 0 0 24px rgba(96,165,250,.14)",
        "glow-violet": "0 0 0 1px rgba(139,92,246,.18), 0 0 24px rgba(139,92,246,.16)",
        panel: "0 20px 60px rgba(2,6,23,.45)"
      },
      backdropBlur: {
        xs: "2px"
      },
      borderRadius: {
        xl: "1rem",
        "2xl": "1.25rem",
        "3xl": "1.5rem"
      },
      backgroundImage: {
        "hero-grid":
            "linear-gradient(rgba(148,163,184,.07) 1px, transparent 1px), linear-gradient(90deg, rgba(148,163,184,.07) 1px, transparent 1px)",
        "panel-grad":
            "linear-gradient(180deg, rgba(255,255,255,.04) 0%, rgba(255,255,255,.01) 100%)",
        "brand-radial":
            "radial-gradient(circle at top left, rgba(34,211,238,.16), transparent 30%), radial-gradient(circle at top right, rgba(139,92,246,.14), transparent 28%), radial-gradient(circle at bottom center, rgba(96,165,250,.10), transparent 35%)"
      },
      backgroundSize: {
        grid: "24px 24px"
      },
      transitionTimingFunction: {
        cinematic: "cubic-bezier(0.22, 1, 0.36, 1)"
      },
      keyframes: {
        floatSoft: {
          "0%, 100%": { transform: "translateY(0px)" },
          "50%": { transform: "translateY(-2px)" }
        },
        pulseGlow: {
          "0%, 100%": { boxShadow: "0 0 0 1px rgba(96,165,250,.14), 0 0 12px rgba(96,165,250,.08)" },
          "50%": { boxShadow: "0 0 0 1px rgba(34,211,238,.22), 0 0 20px rgba(34,211,238,.16)" }
        }
      },
      animation: {
        "float-soft": "floatSoft 4.5s ease-in-out infinite",
        "pulse-glow": "pulseGlow 2.8s ease-in-out infinite"
      },
      maxWidth: {
        "8xl": "96rem"
      }
    }
  },
  plugins: []
};