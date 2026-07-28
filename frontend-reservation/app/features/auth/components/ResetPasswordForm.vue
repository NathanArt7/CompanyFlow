<script setup lang="ts">
import { Lock, Eye, EyeOff } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import { useAuthService } from '../services/auth.service'
import type { ApiError } from '~/composables/useApi'

const props = defineProps<{
  token?: string
}>()

const authService = useAuthService()

const form = ref({
  password: '',
  confirmPassword: '',
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')
const isReset = ref(false)

const isPasswordValid = computed(() => form.value.password.length >= 8)
const passwordsMatch = computed(() =>
  form.value.password.length > 0 && form.value.password === form.value.confirmPassword
)

const handleSubmit = async () => {
  errorMessage.value = ''

  if (!isPasswordValid.value) {
    errorMessage.value = 'Le mot de passe doit contenir au moins 8 caractères.'
    return
  }

  if (!passwordsMatch.value) {
    errorMessage.value = 'Les mots de passe ne correspondent pas.'
    return
  }

  if (!props.token) {
    errorMessage.value = 'Lien de réinitialisation invalide.'
    return
  }

  isSubmitting.value = true
  try {
    await authService.resetPassword({
      token: props.token,
      password: form.value.password,
      password_confirmation: form.value.confirmPassword,
    })
    isReset.value = true
  } catch (e) {
    errorMessage.value = (e as ApiError).message ?? 'Une erreur est survenue. Le lien est peut-être expiré.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="relative w-full min-h-screen flex flex-col items-center justify-center overflow-hidden px-4 py-12 md:py-16">
    <!-- Glow décoratif -->
    <div
      class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[320px] h-[240px] md:w-[600px] md:h-[400px] bg-primary/20 rounded-full blur-[80px] md:blur-[120px]"
    />

    <div class="relative z-10 w-full max-w-md">
      <div class="bg-surface border border-border rounded-2xl px-6 py-8 md:px-10 md:py-10">
        <!-- Logo -->
        <div class="flex items-center justify-center gap-2 mb-8">
          <Logo />
        </div>

        <template v-if="isReset">
          <!-- État succès -->
          <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug text-center">
            Mot de passe réinitialisé
          </h1>
          <p class="text-muted text-sm mt-3 leading-relaxed text-center">
            Votre mot de passe a été mis à jour avec succès. Vous pouvez maintenant vous connecter.
          </p>
          <NuxtLink
            to="/login"
            class="mt-8 block w-full text-center bg-primary hover:bg-primary-hover text-white font-medium py-3 rounded-lg transition-colors"
          >
            Se connecter
          </NuxtLink>
        </template>

        <template v-else>
          <!-- Titre + description -->
          <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug text-center">
            Réinitialisez votre mot de passe
          </h1>
          <p class="text-muted text-sm mt-3 leading-relaxed text-center">
            Choisissez un nouveau mot de passe sécurisé pour votre compte.
          </p>

          <!-- Formulaire -->
          <form class="mt-8 space-y-5" autocomplete="off" @submit.prevent="handleSubmit">
            <input type="text" name="fake-username" autocomplete="username" style="display:none">

            <div>
              <label for="new-password" class="text-foreground text-sm font-medium block mb-2">
                Nouveau mot de passe
              </label>
              <div class="relative">
                <Lock class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
                <input
                  id="new-password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  name="new-password"
                  autocomplete="new-password"
                  placeholder="••••••••"
                  class="w-full bg-background border border-border rounded-lg pl-10 pr-10 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
                >
                <button
                  type="button"
                  class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors"
                  @click="showPassword = !showPassword"
                >
                  <Eye v-if="!showPassword" class="w-4 h-4" />
                  <EyeOff v-else class="w-4 h-4" />
                </button>
              </div>
              <p class="text-muted text-xs mt-2">
                Votre mot de passe doit contenir au minimum 8 caractères.
              </p>
            </div>

            <div>
              <label for="confirm-password" class="text-foreground text-sm font-medium block mb-2">
                Confirmer le mot de passe
              </label>
              <div class="relative">
                <Lock class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
                <input
                  id="confirm-password"
                  v-model="form.confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  name="confirm-password"
                  autocomplete="new-password"
                  placeholder="••••••••"
                  class="w-full bg-background border border-border rounded-lg pl-10 pr-10 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
                >
                <button
                  type="button"
                  class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors"
                  @click="showConfirmPassword = !showConfirmPassword"
                >
                  <Eye v-if="!showConfirmPassword" class="w-4 h-4" />
                  <EyeOff v-else class="w-4 h-4" />
                </button>
              </div>
            </div>

            <p v-if="errorMessage" class="text-red-400 text-sm">
              {{ errorMessage }}
            </p>

            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-3 rounded-lg transition-colors"
            >
              {{ isSubmitting ? 'Réinitialisation en cours...' : 'Réinitialiser le mot de passe' }}
            </button>
          </form>
        </template>
      </div>
    </div>

    <p class="relative z-10 text-center text-muted text-xs mt-8">
      © {{ new Date().getFullYear() }} CompanyFlow. Tous droits réservés.
    </p>
  </div>
</template>
