<script setup lang="ts">
import { Layers } from 'lucide-vue-next'
import { ref } from 'vue'
import { useAuthService } from '../services/auth.service'
import type { ApiError } from '~/composables/useApi'
import type { CreateSuperAdminPayload } from '../type'

const authService = useAuthService()

const form = ref<CreateSuperAdminPayload>({
  lastName: '',
  firstName: '',
  email: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')
const isRegistered = ref(false)

const handleSubmit = async () => {
  errorMessage.value = ''

  if (!form.value.lastName || !form.value.firstName || !form.value.email) {
    errorMessage.value = 'Merci de remplir tous les champs.'
    return
  }

  isSubmitting.value = true
  try {
    await authService.registerSuperAdmin(form.value)
    isRegistered.value = true
  } catch (e) {
    errorMessage.value = (e as ApiError).message ?? 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="relative w-full min-h-screen flex items-center justify-center overflow-hidden px-4 py-12 md:py-16">
    <!-- Glow décoratif, cohérent avec la landing -->
    <div
      class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[320px] h-[240px] md:w-[600px] md:h-[400px] bg-primary/20 rounded-full blur-[80px] md:blur-[120px]"
    />

    <div class="relative z-10 w-full max-w-md">
      <div class="bg-surface border border-border rounded-2xl px-6 py-8 md:px-10 md:py-10">
        <!-- Logo -->
        <div class="flex items-center gap-2 mb-8">
        <Logo />
        </div>

        <template v-if="isRegistered">
          <!-- État succès -->
          <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug">
            Vérifiez votre boîte mail
          </h1>
          <p class="text-muted text-sm mt-3 leading-relaxed">
            Un e-mail d'activation a été envoyé à <strong class="text-foreground">{{ form.email }}</strong>.
            Cliquez sur le lien qu'il contient pour définir votre mot de passe et activer votre compte.
          </p>
        </template>

        <template v-else>
          <!-- Titre + description -->
          <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug">
            Créer votre compte Super Administrateur
          </h1>
          <p class="text-muted text-sm mt-3 leading-relaxed">
            Le Super Administrateur est le responsable principal de votre espace
            CompanyPilot. Ce compte dispose d'un accès complet pour gérer les
            utilisateurs, les salles, les équipements et les paramètres de
            l'entreprise.
          </p>

          <!-- Formulaire -->
          <form class="mt-8 space-y-5" @submit.prevent="handleSubmit">
          <div>
            <label for="lastName" class="text-foreground text-sm font-medium block mb-2">
              Nom
            </label>
            <input
              id="lastName"
              v-model="form.lastName"
              type="text"
              placeholder="Entrez votre nom"
              class="w-full bg-background border border-border rounded-lg px-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label for="firstName" class="text-foreground text-sm font-medium block mb-2">
              Prénom
            </label>
            <input
              id="firstName"
              v-model="form.firstName"
              type="text"
              placeholder="Entrez votre prénom"
              class="w-full bg-background border border-border rounded-lg px-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label for="email" class="text-foreground text-sm font-medium block mb-2">
              Adresse e-mail professionnelle
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="vous@entreprise.com"
              class="w-full bg-background border border-border rounded-lg px-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <p v-if="errorMessage" class="text-red-400 text-sm">
            {{ errorMessage }}
          </p>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-3 rounded-lg transition-colors"
          >
            {{ isSubmitting ? 'Validation en cours...' : 'Valider' }}
          </button>
          </form>
        </template>

         <!-- Lien de connexion -->
        <p class="text-center text-muted text-sm mt-6">
          Vous avez déjà un compte ?
          <br>
          <NuxtLink to="/login" class="text-primary-light font-medium hover:text-primary transition-colors">
            Se connecter
          </NuxtLink>
        </p>
      </div>

      <!-- Bas de page -->
      <p class="text-center text-muted text-xs mt-6">
        Sécurisé et chiffré ·
        <NuxtLink to="/terms" class="hover:text-foreground transition-colors">
          Conditions d'utilisation
        </NuxtLink>
      </p>
    </div>
  </div>
</template>