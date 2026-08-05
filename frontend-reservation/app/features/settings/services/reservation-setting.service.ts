import type { RawReservationSetting, ReservationSettingPayload } from '../type'

export function useReservationSettingService() {
  const { apiFetch } = useApi()

  async function get() {
    const response = await apiFetch<{ message: string, data: RawReservationSetting }>('/reservation-settings')
    return response.data
  }

  async function update(payload: ReservationSettingPayload) {
    const response = await apiFetch<{ message: string, data: RawReservationSetting }>('/reservation-settings', {
      method: 'PUT',
      body: payload,
    })
    return response.data
  }

  return { get, update }
}
