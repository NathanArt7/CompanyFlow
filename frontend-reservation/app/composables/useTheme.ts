export type Theme = 'dark' | 'light'

const STORAGE_KEY = 'theme'

export function useTheme() {
  const theme = useState<Theme>('theme', () => 'dark')

  function apply(value: Theme) {
    theme.value = value

    if (import.meta.client) {
      document.documentElement.classList.toggle('light', value === 'light')
      localStorage.setItem(STORAGE_KEY, value)
    }
  }

  function init() {
    if (!import.meta.client) return
    const stored = localStorage.getItem(STORAGE_KEY)
    apply(stored === 'light' ? 'light' : 'dark')
  }

  function toggle() {
    apply(theme.value === 'dark' ? 'light' : 'dark')
  }

  return { theme, init, toggle }
}
