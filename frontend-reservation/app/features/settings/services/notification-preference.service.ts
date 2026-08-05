import type { NotificationPreferences } from '../type'

export function useNotificationPreferenceService() {
  const { apiFetch } = useApi()

  async function get() {
    const response = await apiFetch<{ data: NotificationPreferences }>('/notification-preferences')
    return response.data
  }

  async function update(payload: NotificationPreferences) {
    const response = await apiFetch<{ message: string, data: NotificationPreferences }>('/notification-preferences', {
      method: 'PUT',
      body: payload,
    })
    return response.data
  }

  return { get, update }
}
