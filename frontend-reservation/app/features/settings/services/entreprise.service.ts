import type { RawEntreprise } from '../type'

export function useEntrepriseService() {
  const { apiFetch } = useApi()

  async function get() {
    const response = await apiFetch<{ data: RawEntreprise }>('/entreprise')
    return response.data
  }

  async function update(formData: FormData) {
    // Envoi via POST + _method=PUT (spoofing Laravel) : un PUT multipart natif
    // ne peuple pas $_FILES côté PHP, nécessaire pour l'upload optionnel du logo.
    formData.append('_method', 'PUT')
    const response = await apiFetch<{ message: string, data: RawEntreprise }>('/entreprise', {
      method: 'POST',
      body: formData,
    })
    return response.data
  }

  return { get, update }
}
