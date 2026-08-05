export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  if (!authStore.isAuthenticated) {
    try {
      await authStore.fetchMe()
    } catch {
      return
    }
  }

  if (authStore.isAuthenticated) {
    // L'Employé n'a pas de page Dashboard dans sa sidebar.
    return navigateTo(authStore.user?.role === 'Employe' ? '/tickets' : '/dashboard')
  }
})
