<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { ref, watch, onMounted } from 'vue'
import type { ActivityModule, RawActivityLog } from '~/features/activity-logs/type'
import { useActivityLogService } from '~/features/activity-logs/services/activity-log.service'

const activityLogService = useActivityLogService()

const activeModule = ref<ActivityModule>('RESERVATION')
const filters = ref({ search: '', from_date: '', to_date: '' })

const page = ref(1)
const perPage = ref(10)

const logs = ref<RawActivityLog[]>([])
const total = ref(0)
const lastPage = ref(1)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function loadLogs() {
  isLoading.value = true
  error.value = null
  try {
    const response = await activityLogService.getLogs({
      module: activeModule.value,
      search: filters.value.search || undefined,
      from_date: filters.value.from_date || undefined,
      to_date: filters.value.to_date || undefined,
      page: page.value,
      per_page: perPage.value,
    })
    logs.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch (e) {
    error.value = (e as { message?: string }).message ?? 'Impossible de charger les journaux d\'activité.'
    logs.value = []
  } finally {
    isLoading.value = false
  }
}

watch(activeModule, () => {
  page.value = 1
  loadLogs()
})

watch(filters, () => {
  page.value = 1
  loadLogs()
}, { deep: true })

watch([page, perPage], loadLogs)
onMounted(loadLogs)

function onFiltersUpdate(value: { search: string, from_date: string, to_date: string }) {
  filters.value = value
}
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold">Journaux d'activité</h1>
      <p class="text-muted text-sm mt-1">
        Consultez le flux global des actions menées par les utilisateurs de votre entreprise.
      </p>
    </div>

    <ActivityLogTabs :active-module="activeModule" @update:active-module="activeModule = $event" />

    <ActivityLogFilters @update:filters="onFiltersUpdate" />

    <ActivityLogTable
      :logs="logs"
      :is-loading="isLoading"
      :error="error"
      :page="page"
      :per-page="perPage"
      :total="total"
      :last-page="lastPage"
      @update:page="page = $event"
      @update:per-page="(v: number) => { perPage = v; page = 1 }"
    />
  </div>
</template>
