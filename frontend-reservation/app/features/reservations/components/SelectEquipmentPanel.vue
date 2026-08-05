<script setup lang="ts">
import { X } from 'lucide-vue-next'
import type { AvailableEquipment } from '../type'

defineProps<{
  isOpen: boolean
  categoryName: string
  equipments: AvailableEquipment[]
  selectedIds: number[]
}>()

const emit = defineEmits<{
  close: []
  toggle: [number]
}>()
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-[60] flex justify-end bg-black/60"
      @click.self="emit('close')"
    >
      <div class="w-full max-w-sm h-full bg-surface border-l border-border flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-border shrink-0">
          <h3 class="text-foreground font-semibold">{{ categoryName }}</h3>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-2">
          <p v-if="equipments.length === 0" class="text-muted text-sm">
            Aucun équipement dans cette catégorie.
          </p>
          <label
            v-for="equipment in equipments"
            :key="equipment.id"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-border transition-colors"
            :class="equipment.disponible ? 'cursor-pointer hover:border-primary/40' : 'cursor-not-allowed opacity-50'"
          >
            <input
              type="checkbox"
              :disabled="!equipment.disponible"
              :checked="selectedIds.includes(equipment.id)"
              @change="emit('toggle', equipment.id)"
            >
            <span class="text-foreground text-sm flex-1">{{ equipment.nom }}</span>
            <span v-if="!equipment.disponible" class="text-muted text-xs">déjà réservé</span>
          </label>
        </div>

        <div class="p-5 border-t border-border shrink-0">
          <button
            type="button"
            class="w-full bg-primary hover:bg-primary-hover text-white font-medium py-2.5 rounded-lg transition-colors"
            @click="emit('close')"
          >
            Valider
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
