<script setup lang="ts">
import { Loader2, X } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { RawEquipmentCategory } from '../type'
import { useEquipmentCategoryService } from '../services/equipment-category.service'

const props = defineProps<{
  isOpen: boolean
  category?: RawEquipmentCategory | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawEquipmentCategory]
}>()

const equipmentCategoryService = useEquipmentCategoryService()

const isEditing = computed(() => !!props.category)

const form = ref({
  nom: '',
  description: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')

function resetForm() {
  form.value = {
    nom: props.category?.nom ?? '',
    description: props.category?.description ?? '',
  }
  errorMessage.value = ''
}

watch(() => props.isOpen, (open) => {
  if (open) resetForm()
})

async function handleSubmit() {
  errorMessage.value = ''

  if (!form.value.nom) {
    errorMessage.value = 'Merci de renseigner un nom.'
    return
  }

  const payload = {
    nom: form.value.nom,
    description: form.value.description || null,
  }

  isSubmitting.value = true
  try {
    const saved = isEditing.value
      ? await equipmentCategoryService.update(props.category!.id, payload)
      : await equipmentCategoryService.create(payload)
    emit('saved', saved)
    emit('close')
  } catch (e) {
    errorMessage.value = (e as ApiError).message ?? 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
      @click.self="emit('close')"
    >
      <div class="bg-surface border border-border rounded-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">
            {{ isEditing ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
          </h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Nom</label>
            <input
              v-model="form.nom"
              type="text"
              placeholder="Ordinateurs"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">
              Description <span class="text-muted font-normal">(optionnel)</span>
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Ordinateurs fixes et portables"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors resize-none"
            />
          </div>

          <p v-if="errorMessage" class="text-red-400 text-sm">
            {{ errorMessage }}
          </p>

          <div class="flex items-center gap-3 pt-2">
            <button
              type="button"
              class="flex-1 bg-background hover:bg-border text-foreground font-medium py-2.5 rounded-lg border border-border transition-colors"
              @click="emit('close')"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg transition-colors"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              {{ isSubmitting ? 'Enregistrement...' : (isEditing ? 'Enregistrer' : 'Créer la catégorie') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
