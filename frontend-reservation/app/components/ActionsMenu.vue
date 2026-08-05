<script setup lang="ts">
import { MoreHorizontal } from 'lucide-vue-next'
import { ref, onMounted, onUnmounted } from 'vue'

withDefaults(defineProps<{
  menuWidth?: string
  bordered?: boolean
}>(), {
  menuWidth: 'w-52',
  bordered: false,
})

const isOpen = ref(false)
const position = ref({ top: 0, right: 0 })
const triggerRef = ref<HTMLButtonElement | null>(null)

// Le menu est téléporté dans <body> et positionné en "fixed" à partir des coordonnées
// réelles du bouton : les cartes/tableaux parents ont un overflow-hidden (coins arrondis,
// scroll horizontal) qui rognerait sinon le menu dès qu'il dépasse leurs bords.
function toggle() {
  if (isOpen.value) {
    isOpen.value = false
    return
  }

  const rect = triggerRef.value!.getBoundingClientRect()
  position.value = {
    top: rect.top - 8,
    right: window.innerWidth - rect.right,
  }
  isOpen.value = true
}

function close() {
  isOpen.value = false
}

onMounted(() => document.addEventListener('click', close))
onUnmounted(() => document.removeEventListener('click', close))
</script>

<template>
  <button
    ref="triggerRef"
    type="button"
    class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-foreground hover:bg-border/50 transition-colors"
    :class="bordered ? 'border border-border' : ''"
    @click.stop="toggle"
  >
    <MoreHorizontal class="w-4 h-4" />
  </button>

  <Teleport to="body">
    <div
      v-if="isOpen"
      :class="menuWidth"
      class="fixed z-50 bg-surface border border-border rounded-lg shadow-lg overflow-hidden"
      :style="{ top: `${position.top}px`, right: `${position.right}px`, transform: 'translateY(-100%)' }"
      @click.stop
    >
      <slot :close="close" />
    </div>
  </Teleport>
</template>
