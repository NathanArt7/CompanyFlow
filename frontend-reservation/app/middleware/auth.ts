export default defineNuxtRouteMiddleware(async () => {
  // Le token vit en sessionStorage (propre à l'onglet, cf. useApi.ts) : invisible du serveur,
  // donc rien à vérifier côté SSR. Le rendu serveur passe toujours à travers ; c'est le
  // AppBootLoader (piloté par useAppBooting) qui masque toute la page jusqu'à ce que cette
  // vérification cliente soit terminée, évitant qu'un visiteur non connecté n'aperçoive la
  // structure de l'app avant sa redirection vers /login.
  if (import.meta.server) return

  const authStore = useAuthStore()
  const companyStore = useCompanyStore()
  const isBooting = useAppBooting()

  try {
    if (!authStore.isAuthenticated) {
      try {
        await authStore.fetchMe()
      } catch {
        // Au tout premier chargement de l'onglet (isBooting encore true), une redirection
        // SPA classique ferait hydrater le client contre le HTML serveur d'une AUTRE page
        // (celle demandée, jamais rendue avec les vraies données puisque non authentifiée) :
        // Vue détecte l'incohérence et casse l'affichage le temps de se rattraper. external:
        // true force un rechargement navigateur complet à la place, qui n'a pas ce problème.
        return navigateTo('/login', isBooting.value ? { external: true } : undefined)
      }
    }

    if (!companyStore.nom) {
      try {
        await companyStore.fetchCompany()
      } catch {
        // Le nom de l'entreprise reste vide ; AppHeader.vue retombe sur un intitulé par défaut.
      }
    }
  } finally {
    // setTimeout 0 : repousse la levée du flag après la passe d'hydratation Vue en cours.
    // Sans ça, si le fetchMe() ci-dessus résout très vite (ex. échec réseau immédiat), la
    // valeur peut changer PENDANT que Vue compare encore le DOM serveur au vdom client,
    // provoquant un avertissement d'hydratation sur AppBootLoader (inoffensif mais bruyant).
    setTimeout(() => { isBooting.value = false }, 0)
  }
})
