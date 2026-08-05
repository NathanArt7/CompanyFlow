<script setup lang="ts">
import { AlertTriangle, Loader2 } from 'lucide-vue-next'

withDefaults(defineProps<{
  isOpen: boolean
  title: string
  message: string
  error?: string | null
  confirmLabel?: string
  cancelLabel?: string
  isLoading?: boolean
}>(), {
  confirmLabel: 'Confirmer',
  cancelLabel: 'Annuler',
  isLoading: false,
})

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
      @click.self="emit('cancel')"
    >
      <div class="bg-surface border border-border rounded-2xl w-full max-w-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-red-500/15 flex items-center justify-center shrink-0">
            <AlertTriangle class="w-5 h-5 text-red-400" />
          </div>
          <h2 class="text-foreground text-base font-semibold">{{ title }}</h2>
        </div>

        <div class="mb-6">
          <p class="text-muted text-sm">{{ message }}</p>
          <p v-if="error" class="text-red-400 text-sm mt-2">{{ error }}</p>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            class="flex-1 bg-background hover:bg-border text-foreground font-medium py-2.5 rounded-lg border border-border transition-colors"
            @click="emit('cancel')"
          >
            {{ cancelLabel }}
          </button>
          <button
            type="button"
            :disabled="isLoading"
            class="flex-1 flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg transition-colors"
            @click="emit('confirm')"
          >
            <Loader2 v-if="isLoading" class="w-4 h-4 animate-spin" />
            {{ confirmLabel }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
