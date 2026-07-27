<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>

<template>
  <div class="space-y-6">
    <AvailabilityHeader />
    <AvailabilityStats />
    <AvailabilityNav />
    <AvailabilityLegend />
    <AvailabilityGrid />

    <EquipmentAvailabilityGrid />
  </div>
</template>