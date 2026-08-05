<script setup lang="ts">
withDefaults(defineProps<{
  horizontal?: boolean
  withBar?: boolean
}>(), {
  horizontal: false,
  withBar: false,
})
</script>

<template>
  <div v-if="horizontal" class="bg-surface border border-border rounded-xl p-5 flex items-center gap-4">
    <Skeleton class="w-12 h-12 rounded-xl shrink-0" />
    <div class="flex-1 space-y-2">
      <Skeleton class="h-6 w-12" />
      <Skeleton class="h-3 w-24" />
    </div>
  </div>

  <div v-else class="bg-surface border border-border rounded-xl p-5">
    <Skeleton class="w-11 h-11 rounded-xl mb-3" />
    <Skeleton class="h-7 w-14 mb-2" />
    <Skeleton class="h-4 w-28 mb-1.5" />
    <Skeleton class="h-3 w-20" />
    <Skeleton v-if="withBar" class="h-1 w-full rounded-full mt-3" />
  </div>
</template>
