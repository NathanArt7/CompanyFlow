<script setup lang="ts">
import { X, Loader2 } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { RawEntreprise } from '../type'
import { useEntrepriseService } from '../services/entreprise.service'

const props = defineProps<{
  isOpen: boolean
  entreprise: RawEntreprise | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawEntreprise]
}>()

const entrepriseService = useEntrepriseService()
const toast = useToast()

const form = ref({
  nom: '',
  telephone: '',
  adresse: '',
  site_web: '',
})

const logoFile = ref<File | null>(null)
const isSubmitting = ref(false)
const errorMessage = ref('')

function resetForm() {
  form.value = {
    nom: props.entreprise?.nom ?? '',
    telephone: props.entreprise?.telephone ?? '',
    adresse: props.entreprise?.adresse ?? '',
    site_web: props.entreprise?.site_web ?? '',
  }
  logoFile.value = null
  errorMessage.value = ''
}

watch(() => props.isOpen, (open) => {
  if (open) resetForm()
})

function onLogoChange(event: Event) {
  const input = event.target as HTMLInputElement
  logoFile.value = input.files?.[0] ?? null
}

async function handleSubmit() {
  errorMessage.value = ''

  if (!form.value.nom) {
    errorMessage.value = "Merci de renseigner le nom de l'entreprise."
    return
  }
  if (!form.value.telephone) {
    errorMessage.value = 'Merci de renseigner un numéro de téléphone.'
    return
  }
  if (!form.value.adresse) {
    errorMessage.value = 'Merci de renseigner une adresse.'
    return
  }

  const formData = new FormData()
  formData.append('nom', form.value.nom)
  formData.append('telephone', form.value.telephone)
  formData.append('adresse', form.value.adresse)
  if (form.value.site_web) formData.append('site_web', form.value.site_web)
  if (logoFile.value) formData.append('logo', logoFile.value)

  isSubmitting.value = true
  try {
    const saved = await entrepriseService.update(formData)
    emit('saved', saved)
    toast.success('Informations de l\'entreprise modifiées avec succès.')
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
      <div class="bg-surface border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">Modifier l'entreprise</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Nom de l'entreprise</label>
            <input
              v-model="form.nom"
              type="text"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Téléphone</label>
            <input
              v-model="form.telephone"
              type="text"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Adresse</label>
            <input
              v-model="form.adresse"
              type="text"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">
              Site web <span class="text-muted font-normal">(optionnel)</span>
            </label>
            <input
              v-model="form.site_web"
              type="url"
              placeholder="https://exemple.com"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">
              Logo <span class="text-muted font-normal">(optionnel)</span>
            </label>
            <input
              type="file"
              accept=".jpg,.jpeg,.png,.svg,.webp"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-primary file:text-white file:text-xs focus:outline-none focus:border-primary transition-colors"
              @change="onLogoChange"
            >
            <p class="text-muted text-xs mt-1">PNG, JPG, SVG ou WEBP. Max 2MB.</p>
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
              {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
