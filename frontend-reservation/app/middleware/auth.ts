export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  if (!authStore.isAuthenticated) {
    try {
      await authStore.fetchMe()
    } catch {
      return navigateTo('/login')
    }
  }
})
