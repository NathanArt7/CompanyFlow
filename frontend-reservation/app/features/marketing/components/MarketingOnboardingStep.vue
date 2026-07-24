<script setup lang="ts">
import type { Component } from 'vue'

defineProps<{
  number: number
  icon: Component
  title: string
  description: string
}>()
</script>

<template>
  <div class="flex flex-col items-center text-center">
    <div class="relative">
      <div class="w-16 h-16 rounded-xl bg-surface border border-border flex items-center justify-center">
        <component :is="icon" class="w-6 h-6 text-foreground" />
      </div>
      <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">
        {{ number }}
      </span>
    </div>
    <h3 class="text-foreground font-semibold mt-4">{{ title }}</h3>
    <p class="text-muted text-sm mt-2 max-w-[220px]">{{ description }}</p>
  </div>
</template>