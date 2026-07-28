<script setup lang="ts">
import { Mail } from 'lucide-vue-next'
import { ref } from 'vue'
import { useAuthService } from '../services/auth.service'
import type { ApiError } from '~/composables/useApi'

const authService = useAuthService()

const form = ref({
  email: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')
const isSubmitted = ref(false)

const handleSubmit = async () => {
  errorMessage.value = ''

  if (!form.value.email) {
    errorMessage.value = 'Merci de renseigner votre adresse e-mail.'
    return
  }

  isSubmitting.value = true
  try {
    await authService.forgotPassword(form.value.email)
    isSubmitted.value = true
  } catch (e) {
    errorMessage.value = (e as ApiError).message ?? 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="relative w-full min-h-screen flex items-center justify-center overflow-hidden px-4 py-12 md:py-16">
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

        <template v-if="isSubmitted">
          <!-- État succès -->
          <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug text-center">
            Vérifiez votre boîte mail
          </h1>
          <p class="text-muted text-sm mt-3 leading-relaxed text-center">
            Si un compte est associé à <strong class="text-foreground">{{ form.email }}</strong>, un lien de
            réinitialisation vient de lui être envoyé.
          </p>
        </template>

        <template v-else>
          <!-- Titre + description -->
          <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug text-center">
            Mot de passe oublié ?
          </h1>
          <p class="text-muted text-sm mt-3 leading-relaxed text-center">
            Indiquez votre adresse e-mail, nous vous enverrons un lien pour réinitialiser votre mot de passe.
          </p>

          <!-- Formulaire -->
          <form class="mt-8 space-y-5" @submit.prevent="handleSubmit">
            <div>
              <label for="email" class="text-foreground text-sm font-medium block mb-2">
                Adresse e-mail
              </label>
              <div class="relative">
                <Mail class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="vous@entreprise.com"
                  class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
                >
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
              {{ isSubmitting ? 'Envoi en cours...' : 'Envoyer le lien' }}
            </button>
          </form>
        </template>

        <!-- Lien de connexion -->
        <p class="text-center text-muted text-sm mt-6">
          <NuxtLink to="/login" class="text-primary-light font-medium hover:text-primary transition-colors">
            Retour à la connexion
          </NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>
