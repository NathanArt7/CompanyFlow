<script setup lang="ts">
import type { Component } from 'vue'
import { TrendingUp } from 'lucide-vue-next'

defineProps<{
  icon: Component
  iconBg: string
  label: string
  value: string | number
  trend?: {
    value: string
    positive: boolean
  }
  subtext?: string
}>()
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div
      class="w-11 h-11 rounded-xl flex items-center justify-center mb-4"
      :class="iconBg"
    >
      <component :is="icon" class="w-5 h-5 text-white" />
    </div>

    <p class="text-muted text-sm mb-2">{{ label }}</p>
    <p class="text-foreground text-3xl font-bold">{{ value }}</p>

    <div v-if="trend" class="flex items-center gap-1 mt-2">
      <TrendingUp
        class="w-3.5 h-3.5"
        :class="trend.positive ? 'text-green-500' : 'text-red-400'"
      />
      <span
        class="text-xs font-medium"
        :class="trend.positive ? 'text-green-500' : 'text-red-400'"
      >
        {{ trend.value }}
      </span>
      <span class="text-muted text-xs">vs hier</span>
    </div>

    <p v-else-if="subtext" class="text-muted text-xs mt-2">
      {{ subtext }}
    </p>
  </div>
</template>