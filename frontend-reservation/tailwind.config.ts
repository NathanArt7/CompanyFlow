export default {
  darkMode: 'class',
  content: [
    './app/components/**/*.{vue,js,ts}',
    './app/features/**/*.{vue,js,ts}',
    './app/pages/**/*.vue',
    './app/layouts/**/*.vue',
    './app/app.vue',
  ],
  theme: {
    extend: {
      colors: {
        background: '#0a0a0f',
        surface: '#111117',
        border: '#1f1f28',
        primary: {
          DEFAULT: '#6366f1',
          hover: '#5457e5',
          light: '#818cf8',
        },
        muted: '#9ca3af',
        foreground: '#f5f5f7',
      },
      borderRadius: {
        DEFAULT: '0.75rem',
      },
    },
  },
}