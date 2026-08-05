import type { PaginatedResponse, RawNotification } from '../type'

export function useNotificationService() {
  const { apiFetch } = useApi()

  async function getNotifications(page = 1) {
    return apiFetch<PaginatedResponse<RawNotification>>('/notifications', { query: { page } })
  }

  async function getUnreadCount() {
    const response = await apiFetch<{ count: number }>('/notifications/unread-count')
    return response.count
  }

  async function markAsRead(id: string) {
    return apiFetch<{ message: string }>(`/notifications/${id}/read`, { method: 'PATCH' })
  }

  async function markAllAsRead() {
    return apiFetch<{ message: string }>('/notifications/read-all', { method: 'PATCH' })
  }

  return { getNotifications, getUnreadCount, markAsRead, markAllAsRead }
}
