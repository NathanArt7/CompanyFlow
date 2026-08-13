<script setup lang="ts">
import { CheckCircle2, XCircle, X } from 'lucide-vue-next'

const { toasts, dismiss } = useToast()
</script>

<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[400] flex flex-col gap-2 w-full max-w-sm pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-start gap-3 bg-surface border border-border rounded-2xl px-4 py-3 shadow-lg"
        >
          <CheckCircle2 v-if="toast.type === 'success'" class="w-5 h-5 text-green-500 shrink-0 mt-0.5" />
          <XCircle v-else class="w-5 h-5 text-red-400 shrink-0 mt-0.5" />
          <p class="flex-1 text-foreground text-sm">{{ toast.message }}</p>
          <button
            class="text-muted hover:text-foreground transition-colors shrink-0"
            title="Fermer"
            @click="dismiss(toast.id)"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(1rem);
}
.toast-leave-active {
  position: absolute;
  width: 100%;
}
</style>
