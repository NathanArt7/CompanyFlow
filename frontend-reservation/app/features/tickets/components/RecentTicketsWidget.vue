<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { RawTicket } from '../type'
import { useTicketService } from '../services/ticket.service'

const ticketService = useTicketService()

const tickets = ref<RawTicket[]>([])
const isLoading = ref(true)
const error = ref<string | null>(null)

onMounted(async () => {
  try {
    const response = await ticketService.getTickets({ per_page: 5 })
    tickets.value = response.data
  } catch {
    error.value = 'Impossible de charger les tickets.'
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-foreground font-semibold">Derniers tickets</h2>
      <NuxtLink
        to="/tickets"
        class="text-xs bg-background border border-border rounded-lg px-3 py-1.5 text-foreground hover:border-primary/40 transition-colors whitespace-nowrap"
      >
        Voir tous les tickets
      </NuxtLink>
    </div>

    <div v-if="isLoading" class="space-y-3">
      <Skeleton v-for="i in 3" :key="i" class="h-12 w-full" />
    </div>
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>
    <p v-else-if="tickets.length === 0" class="text-muted text-xs">
      Aucun ticket pour le moment.
    </p>
    <div v-else class="space-y-3">
      <div
        v-for="ticket in tickets"
        :key="ticket.id"
        class="flex items-center justify-between gap-3 pb-3 border-b border-border last:border-0 last:pb-0"
      >
        <div class="min-w-0">
          <p class="text-foreground text-sm font-medium truncate">{{ ticket.equipment?.nom ?? '—' }}</p>
          <p class="text-muted text-xs truncate">{{ ticket.description }}</p>
        </div>
        <TicketStatusBadge :status="ticket.statut" />
      </div>
    </div>
  </div>
</template>
