<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>

<template>
  <div class="space-y-6">
    <ReservationsHeader />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <WeekStrip />
      <DailySummary />
    </div>

    <div class="bg-surface border border-border rounded-xl p-5">
      <ReservationsFilters />
    </div>

    <ReservationsTable />
  </div>
</template>