<script setup lang="ts">
import { Layers, Mail, Lock, Eye, EyeOff } from 'lucide-vue-next'
import { ref } from 'vue'

const form = ref({
  email: '',
  password: '',
})

const showPassword = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')

const handleSubmit = async () => {
  errorMessage.value = ''

  if (!form.value.email || !form.value.password) {
    errorMessage.value = 'Merci de remplir tous les champs.'
    return
  }

  isSubmitting.value = true
  try {
    // À brancher sur ton service API réel, ex:
    // await authService.login(form.value)
  } catch (e) {
    errorMessage.value = 'Email ou mot de passe incorrect.'
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

        <!-- Titre + description -->
        <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug text-center">
          Connectez-vous à votre espace
        </h1>
        <p class="text-muted text-sm mt-3 leading-relaxed text-center">
          Accédez à votre espace CompanyFlow
        </p>

        <!-- Formulaire -->
        <form class="mt-8 space-y-5" autocomplete="off" @submit.prevent="handleSubmit">
            <!-- Leurres invisibles pour absorber l'autofill de Chrome -->
            <input type="text" name="fake-username" autocomplete="username" style="display:none">
            <input type="password" name="fake-password" autocomplete="new-password" style="display:none">
          <div>
            <label for="email" class="text-foreground text-sm font-medium block mb-2">
              Adresse e-mail
            </label>
            <div class="relative">
              <Mail class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2" />
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="vous@entreprise.com"
                class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div>
            <label for="password" class="text-foreground text-sm font-medium block mb-2">
              Mot de passe
            </label>
            <div class="relative">
              <Lock class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2" />
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
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
          </div>

          <div class="flex justify-end">
            <NuxtLink to="/forgot-password" class="text-primary-light text-sm hover:text-primary transition-colors">
              Mot de passe oublié ?
            </NuxtLink>
          </div>

          <p v-if="errorMessage" class="text-red-400 text-sm">
            {{ errorMessage }}
          </p>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-3 rounded-lg transition-colors"
          >
            {{ isSubmitting ? 'Connexion en cours...' : 'Se connecter' }}
          </button>
        </form>

        <!-- Lien inscription -->
        <p class="text-center text-muted text-sm mt-6">
          Vous n'avez pas encore d'entreprise ?
          <br>
          <NuxtLink to="/signup" class="text-primary-light font-medium hover:text-primary transition-colors">
            Créer mon entreprise
          </NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>