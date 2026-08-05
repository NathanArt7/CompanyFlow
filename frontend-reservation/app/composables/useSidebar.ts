export function useSidebar() {
  const isCollapsed = useState('sidebar-collapsed', () => false)
  const isMobileOpen = useState('sidebar-mobile-open', () => false)

  function toggleMobile() {
    isMobileOpen.value = !isMobileOpen.value
  }

  function closeMobile() {
    isMobileOpen.value = false
  }

  return { isCollapsed, isMobileOpen, toggleMobile, closeMobile }
}
