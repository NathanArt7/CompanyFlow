<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })
</script>

<template>
  <div class="space-y-6">
    <EquipmentsHeader />
    <EquipmentsStats />

    <div class="bg-surface border border-border rounded-xl p-5">
      <EquipmentsFilters />
    </div>

    <EquipmentsTable />
  </div>
</template>