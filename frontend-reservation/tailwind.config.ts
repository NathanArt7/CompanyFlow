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
        background: 'var(--color-background)',
        surface: 'var(--color-surface)',
        border: 'var(--color-border)',
        primary: {
          DEFAULT: '#6366f1',
          hover: '#5457e5',
          light: '#818cf8',
        },
        muted: 'var(--color-muted)',
        foreground: 'var(--color-foreground)',
        skeleton: 'var(--color-skeleton)',
        'grid-line': 'var(--color-grid-line)',
      },
      borderRadius: {
        DEFAULT: '0.75rem',
      },
    },
  },
}