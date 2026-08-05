<script setup lang="ts">
import { Ticket, Monitor, Calendar, Bell as BellIcon } from 'lucide-vue-next'
import { onMounted } from 'vue'
import type { RawNotification } from '~/features/notifications/type'

const notificationStore = useNotificationStore()

onMounted(() => {
  notificationStore.fetchRecent()
})

function formatDate(value: string) {
  return new Date(value).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

function iconFor(notification: RawNotification) {
  if (notification.type.includes('Ticket')) return Ticket
  if (notification.type.includes('Equipment')) return Monitor
  if (notification.type.includes('Reservation')) return Calendar
  return BellIcon
}
</script>

<template>
  <div class="absolute right-0 top-full mt-2 w-80 max-w-[calc(100vw-2rem)] bg-surface border border-border rounded-lg shadow-lg overflow-hidden z-10">
    <div class="flex items-center justify-between px-4 py-3 border-b border-border">
      <h3 class="text-foreground text-sm font-semibold">Notifications</h3>
      <button
        v-if="notificationStore.unreadCount > 0"
        class="text-primary-light text-xs hover:text-primary transition-colors"
        @click="notificationStore.markAllAsRead"
      >
        Tout marquer comme lu
      </button>
    </div>

    <div class="max-h-96 overflow-y-auto">
      <p v-if="notificationStore.isLoading" class="text-muted text-xs p-4">
        Chargement...
      </p>
      <p v-else-if="notificationStore.items.length === 0" class="text-muted text-xs p-4">
        Aucune notification pour le moment.
      </p>

      <button
        v-for="notification in notificationStore.items"
        :key="notification.id"
        class="w-full flex items-start gap-3 px-4 py-3 text-left border-b border-border last:border-0 hover:bg-background/40 transition-colors"
        @click="notificationStore.markAsRead(notification.id)"
      >
        <div
          class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
          :class="notification.read_at ? 'bg-border/50 text-muted' : 'bg-primary/15 text-primary-light'"
        >
          <component :is="iconFor(notification)" class="w-4 h-4" />
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-foreground text-sm font-medium truncate">{{ notification.data.title }}</p>
          <p class="text-muted text-xs mt-0.5 break-words">{{ notification.data.message }}</p>
          <p class="text-muted text-xs mt-1">{{ formatDate(notification.created_at) }}</p>
        </div>
        <span v-if="!notification.read_at" class="w-2 h-2 rounded-full bg-primary shrink-0 mt-1.5" />
      </button>
    </div>
  </div>
</template>
