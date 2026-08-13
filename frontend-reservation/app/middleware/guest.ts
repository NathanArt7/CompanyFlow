export default defineNuxtRouteMiddleware(async () => {
  // Cf. middleware/auth.ts : token en sessionStorage, invisible du serveur, donc rien à
  // vérifier côté SSR ici non plus.
  if (import.meta.server) return

  const authStore = useAuthStore()
  const isBooting = useAppBooting()

  try {
    if (!authStore.isAuthenticated) {
      try {
        await authStore.fetchMe()
      } catch {
        return
      }
    }

    if (authStore.isAuthenticated) {
      // external au premier chargement : cf. le commentaire équivalent dans auth.ts.
      const target = authStore.user?.role === 'Employe' ? '/tickets' : '/dashboard'
      return navigateTo(target, isBooting.value ? { external: true } : undefined)
    }
  } finally {
    // Cf. le commentaire équivalent dans auth.ts.
    setTimeout(() => { isBooting.value = false }, 0)
  }
})
