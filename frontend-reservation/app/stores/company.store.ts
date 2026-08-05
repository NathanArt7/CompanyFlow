export const useCompanyStore = defineStore('company', () => {
  const nom = ref<string | null>(null)
  const logo = ref<string | null>(null)

  function setCompany(data: { nom?: string | null, logo?: string | null }) {
    nom.value = data.nom ?? null
    logo.value = data.logo ?? null
  }

  async function fetchCompany() {
    const { useEntrepriseService } = await import('~/features/settings/services/entreprise.service')
    const entreprise = await useEntrepriseService().get()
    setCompany({ nom: entreprise.nom, logo: entreprise.logo })
  }

  return { nom, logo, setCompany, fetchCompany }
})
