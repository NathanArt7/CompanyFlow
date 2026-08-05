<script setup lang="ts">
import { Shield } from 'lucide-vue-next'
import { ref } from 'vue'

const isModalOpen = ref(false)
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5 flex items-start justify-between gap-4">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
        <Shield class="w-4.5 h-4.5 text-primary-light" />
      </div>
      <div>
        <h2 class="text-foreground font-semibold">Mot de passe</h2>
        <p class="text-muted text-xs mt-0.5">Modifier votre mot de passe.</p>
      </div>
    </div>
    <button
      class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors shrink-0"
      @click="isModalOpen = true"
    >
      Gérer
    </button>

    <ChangePasswordModal
      :is-open="isModalOpen"
      @close="isModalOpen = false"
    />
  </div>
</template>
