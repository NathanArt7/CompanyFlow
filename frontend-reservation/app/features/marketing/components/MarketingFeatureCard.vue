<script setup lang="ts">
import type { Component } from 'vue'

defineProps<{
  icon: Component
  title: string
  description: string
}>()
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-6 hover:border-primary/40 transition-colors">
    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
      <component :is="icon" class="w-5 h-5 text-primary" />
    </div>
    <h3 class="text-foreground font-semibold text-lg mb-2">{{ title }}</h3>
    <p class="text-muted text-sm leading-relaxed">{{ description }}</p>
  </div>
</template>