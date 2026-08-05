<script setup lang="ts">
import { X, Loader2, User as UserIcon } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import type { ApiError } from '~/composables/useApi'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  close: []
}>()

const authStore = useAuthStore()

const form = ref({
  nom: '',
  prenom: '',
})

const photoFile = ref<File | null>(null)
const photoPreview = ref<string | null>(null)
const isSubmitting = ref(false)
const errorMessage = ref('')

const config = useRuntimeConfig()
const currentPhotoUrl = computed(() => {
  const photo = authStore.user?.photo
  if (!photo) return null
  return `${config.public.apiBase.replace(/\/api\/?$/, '')}/storage/${photo}`
})

const initials = computed(() => {
  const user = authStore.user
  if (!user) return ''
  return `${user.prenom.charAt(0)}${user.nom.charAt(0)}`.toUpperCase()
})

function resetForm() {
  form.value = {
    nom: authStore.user?.nom ?? '',
    prenom: authStore.user?.prenom ?? '',
  }
  photoFile.value = null
  photoPreview.value = null
  errorMessage.value = ''
}

watch(() => props.isOpen, (open) => {
  if (open) resetForm()
})

function onPhotoChange(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0] ?? null
  photoFile.value = file
  photoPreview.value = file ? URL.createObjectURL(file) : null
}

async function handleSubmit() {
  errorMessage.value = ''

  if (!form.value.nom) {
    errorMessage.value = 'Merci de renseigner un nom.'
    return
  }
  if (!form.value.prenom) {
    errorMessage.value = 'Merci de renseigner un prénom.'
    return
  }

  const formData = new FormData()
  formData.append('nom', form.value.nom)
  formData.append('prenom', form.value.prenom)
  if (photoFile.value) formData.append('photo', photoFile.value)

  isSubmitting.value = true
  try {
    await authStore.updateProfile(formData)
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
      <div class="bg-surface border border-border rounded-2xl w-full max-w-sm max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">Modifier mon profil</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div class="flex flex-col items-center gap-3">
            <div class="w-20 h-20 rounded-full border border-border bg-background flex items-center justify-center overflow-hidden">
              <img v-if="photoPreview || currentPhotoUrl" :src="photoPreview ?? currentPhotoUrl!" alt="" class="w-full h-full object-cover">
              <span v-else-if="initials" class="text-primary-light text-lg font-semibold">{{ initials }}</span>
              <UserIcon v-else class="w-8 h-8 text-primary-light" />
            </div>
            <label class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors cursor-pointer">
              Changer la photo
              <input type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden" @change="onPhotoChange">
            </label>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Prénom</label>
            <input
              v-model="form.prenom"
              type="text"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Nom</label>
            <input
              v-model="form.nom"
              type="text"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
            >
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
