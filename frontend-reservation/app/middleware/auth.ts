export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()
  const companyStore = useCompanyStore()

  if (!authStore.isAuthenticated) {
    try {
      await authStore.fetchMe()
    } catch {
      return navigateTo('/login')
    }
  }

  if (!companyStore.nom) {
    try {
      await companyStore.fetchCompany()
    } catch {
      // Le nom de l'entreprise reste vide ; AppHeader.vue retombe sur un intitulé par défaut.
    }
  }
})
