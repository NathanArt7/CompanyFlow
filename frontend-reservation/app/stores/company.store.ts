export const useCompanyStore = defineStore('company', () => {
  const nom = ref<string | null>(null)
  const logo = ref<string | null>(null)

  // Alimenté par la réponse de POST /entreprise/configuration.
  // Pas d'endpoint GET /entreprise pour re-fetch après un reload sur une autre session :
  // AppHeader.vue doit prévoir un fallback (nom de l'utilisateur connecté) tant que ce gap n'est pas comblé côté backend.
  function setCompany(data: { nom?: string | null, logo?: string | null }) {
    nom.value = data.nom ?? null
    logo.value = data.logo ?? null
  }

  return { nom, logo, setCompany }
})
