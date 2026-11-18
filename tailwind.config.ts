import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        paper: {
          50: "#faf8f5",
          100: "#f5f1ea",
          200: "#eae2d5",
          300: "#dbcdb9",
          800: "#2d2823",
          900: "#1b1815",
        },
        newspaper: {
          ink: "#121212",
          crimson: "#b91c1c",
          navy: "#0f172a",
          border: "#e2e8f0",
          muted: "#64748b",
        },
      },
      fontFamily: {
        serif: ["var(--font-serif)", "var(--font-malayalam)", "Georgia", "Cambria", "Times New Roman", "serif"],
        sans: ["var(--font-malayalam)", "var(--font-sans)", "Inter", "system-ui", "sans-serif"],
        ml: ["var(--font-malayalam)", "sans-serif"],
      },
      boxShadow: {
        paper: "0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04)",
        scan: "0 10px 40px -10px rgba(0, 0, 0, 0.25)",
      },
    },
  },
  plugins: [],
};
export default config;
