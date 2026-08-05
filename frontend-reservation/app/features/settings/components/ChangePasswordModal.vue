<script setup lang="ts">
import { X, Loader2, Check, Eye, EyeOff } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import type { ApiError } from '~/composables/useApi'
import { useAuthService } from '~/features/auth/services/auth.service'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  close: []
}>()

const authService = useAuthService()

const form = ref({
  currentPassword: '',
  password: '',
  passwordConfirmation: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showPasswordConfirmation = ref(false)

watch(() => props.isOpen, (open) => {
  if (open) {
    form.value = { currentPassword: '', password: '', passwordConfirmation: '' }
    errorMessage.value = ''
    successMessage.value = ''
  }
})

async function handleSubmit() {
  errorMessage.value = ''
  successMessage.value = ''

  if (!form.value.currentPassword) {
    errorMessage.value = 'Merci de renseigner votre mot de passe actuel.'
    return
  }
  if (form.value.password.length < 8) {
    errorMessage.value = 'Le nouveau mot de passe doit contenir au moins 8 caractères.'
    return
  }
  if (form.value.password !== form.value.passwordConfirmation) {
    errorMessage.value = 'Les mots de passe ne correspondent pas.'
    return
  }

  isSubmitting.value = true
  try {
    await authService.changePassword(form.value.currentPassword, form.value.password, form.value.passwordConfirmation)
    successMessage.value = 'Mot de passe modifié avec succès.'
    form.value = { currentPassword: '', password: '', passwordConfirmation: '' }
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
      <div class="bg-surface border border-border rounded-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">Modifier le mot de passe</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Mot de passe actuel</label>
            <div class="relative">
              <input
                v-model="form.currentPassword"
                :type="showCurrentPassword ? 'text' : 'password'"
                autocomplete="current-password"
                class="w-full bg-background border border-border rounded-lg pl-3 pr-10 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors"
                :title="showCurrentPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                @click="showCurrentPassword = !showCurrentPassword"
              >
                <Eye v-if="!showCurrentPassword" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
              </button>
            </div>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Nouveau mot de passe</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showNewPassword ? 'text' : 'password'"
                autocomplete="new-password"
                class="w-full bg-background border border-border rounded-lg pl-3 pr-10 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors"
                :title="showNewPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                @click="showNewPassword = !showNewPassword"
              >
                <Eye v-if="!showNewPassword" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
              </button>
            </div>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Confirmer le mot de passe</label>
            <div class="relative">
              <input
                v-model="form.passwordConfirmation"
                :type="showPasswordConfirmation ? 'text' : 'password'"
                autocomplete="new-password"
                class="w-full bg-background border border-border rounded-lg pl-3 pr-10 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors"
                :title="showPasswordConfirmation ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                @click="showPasswordConfirmation = !showPasswordConfirmation"
              >
                <Eye v-if="!showPasswordConfirmation" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
              </button>
            </div>
          </div>

          <p v-if="errorMessage" class="text-red-400 text-sm">
            {{ errorMessage }}
          </p>
          <p v-if="successMessage" class="flex items-center gap-1.5 text-green-500 text-sm">
            <Check class="w-4 h-4" />
            {{ successMessage }}
          </p>

          <div class="flex items-center gap-3 pt-2">
            <button
              type="button"
              class="flex-1 bg-background hover:bg-border text-foreground font-medium py-2.5 rounded-lg border border-border transition-colors"
              @click="emit('close')"
            >
              Fermer
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
