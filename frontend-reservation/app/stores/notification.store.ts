import { useNotificationService } from '~/features/notifications/services/notification.service'
import type { RawNotification } from '~/features/notifications/type'

export const useNotificationStore = defineStore('notification', () => {
  const items = ref<RawNotification[]>([])
  const unreadCount = ref(0)
  const isLoading = ref(false)

  async function fetchUnreadCount() {
    const notificationService = useNotificationService()
    try {
      unreadCount.value = await notificationService.getUnreadCount()
    } catch {
      // Échec silencieux : le badge garde sa dernière valeur connue plutôt que de casser le header.
    }
  }

  async function fetchRecent() {
    const notificationService = useNotificationService()
    isLoading.value = true
    try {
      const response = await notificationService.getNotifications(1)
      items.value = response.data
    } catch {
      items.value = []
    } finally {
      isLoading.value = false
    }
  }

  async function markAsRead(id: string) {
    const notificationService = useNotificationService()
    const notification = items.value.find(item => item.id === id)
    if (!notification || notification.read_at) return

    notification.read_at = new Date().toISOString()
    unreadCount.value = Math.max(0, unreadCount.value - 1)

    try {
      await notificationService.markAsRead(id)
    } catch {
      // La resynchronisation se fera au prochain fetch ; on ne bloque pas l'utilisateur.
    }
  }

  async function markAllAsRead() {
    const notificationService = useNotificationService()
    items.value = items.value.map(item => ({ ...item, read_at: item.read_at ?? new Date().toISOString() }))
    unreadCount.value = 0

    try {
      await notificationService.markAllAsRead()
    } catch {
      // idem : resynchronisation au prochain fetch
    }
  }

  return { items, unreadCount, isLoading, fetchUnreadCount, fetchRecent, markAsRead, markAllAsRead }
})
