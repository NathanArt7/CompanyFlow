<script setup lang="ts">
import { Building2, User, Mail, Settings } from 'lucide-vue-next'
import type { OnboardingStepData } from './type'

const steps: OnboardingStepData[] = [
  {
    number: 1,
    icon: Building2,
    title: 'Créez votre entreprise',
    description: 'En quelques clics, créez le profil de votre organisation sur CompanyFlow.',
  },
  {
    number: 2,
    icon: User,
    title: 'Informations du Super Administrateur',
    description: 'Renseignez les coordonnées du responsable qui administrera la plateforme.',
  },
  {
    number: 3,
    icon: Mail,
    title: "Email d'activation",
    description: 'Recevez un email de confirmation pour activer votre compte en toute sécurité.',
  },
  {
    number: 4,
    icon: Settings,
    title: 'Configurez et démarrez',
    description: 'Personnalisez vos espaces, invitez vos équipes et commencez à utiliser CompanyFlow.',
  },
]
</script>

<template>
  <section id="works" class="w-full px-6 md:px-10 py-16 md:py-20 text-center">
    <span class="text-primary-light text-sm font-semibold tracking-wide uppercase">Démarrage</span>
    <h2 class="text-foreground text-2xl sm:text-3xl md:text-4xl font-bold mt-3">
      Lancez-vous en 4 étapes
    </h2>
    <p class="text-muted text-base md:text-lg mt-2 max-w-lg mx-auto px-2">
      Une mise en place simple et rapide pour commencer à organiser votre entreprise.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10 mt-12 md:mt-16">
      <MarketingOnboardingStep
        v-for="step in steps"
        :key="step.number"
        :number="step.number"
        :icon="step.icon"
        :title="step.title"
        :description="step.description"
      />
    </div>
  </section>
</template>