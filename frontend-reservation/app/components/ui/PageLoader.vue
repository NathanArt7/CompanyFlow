<script setup lang="ts">
import { Vue3Lottie } from 'vue3-lottie'
import animationData from '~/assets/animations/loading-3-dots.json'

const isLoading = usePageLoading()
</script>

<template>
  <Transition name="page-loader-fade">
    <div
      v-if="isLoading"
      class="absolute inset-0 z-[200] flex items-center justify-center bg-background"
    >
      <ClientOnly>
        <Vue3Lottie :animation-data="animationData" :height="190" :width="190" class="-translate-y-16" />
      </ClientOnly>
    </div>
  </Transition>
</template>

<style scoped>
.page-loader-fade-enter-active,
.page-loader-fade-leave-active {
  transition: opacity 0.15s ease;
}
.page-loader-fade-enter-from,
.page-loader-fade-leave-to {
  opacity: 0;
}
</style>
