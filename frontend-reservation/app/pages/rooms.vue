<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>

<template>
  <div class="space-y-6">
    <RoomsHeader />
    <RoomsStats />

    <div class="bg-surface border border-border rounded-xl p-5">
      <RoomsFilters />
    </div>

    <RoomsTable />
  </div>
</template>