<script setup lang="ts">
withDefaults(defineProps<{
  rows?: number
  columns?: number
}>(), {
  rows: 5,
  columns: 5,
})
</script>

<template>
  <div class="p-5 space-y-5">
    <div v-for="r in rows" :key="r" class="flex items-center gap-4">
      <Skeleton
        v-for="c in columns"
        :key="c"
        class="h-4 flex-1"
        :style="{ maxWidth: c === 1 ? '140px' : '100px' }"
      />
    </div>
  </div>
</template>
