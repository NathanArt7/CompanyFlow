<script setup lang="ts">
import { Sun, Moon } from 'lucide-vue-next'

const { theme, toggle } = useTheme()
</script>

<template>
  <button
    class="text-muted hover:text-foreground transition-colors"
    :title="theme === 'dark' ? 'Passer au thème clair' : 'Passer au thème sombre'"
    @click="toggle"
  >
    <Sun v-if="theme === 'dark'" class="w-5 h-5" />
    <Moon v-else class="w-5 h-5" />
  </button>
</template>
