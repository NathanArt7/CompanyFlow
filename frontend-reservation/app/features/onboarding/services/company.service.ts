import type { ReservationSettingsPayload } from '../type'

export function useCompanyService() {
  const { apiFetch } = useApi()

  function configure(formData: FormData) {
    return apiFetch<{ message: string, data: unknown }>('/entreprise/configuration', {
      method: 'POST',
      body: formData,
    })
  }

  function setReservationSettings(payload: ReservationSettingsPayload) {
    return apiFetch<{ message: string, data: unknown }>('/reservation-settings', {
      method: 'PUT',
      body: payload,
    })
  }

  return { configure, setReservationSettings }
}
