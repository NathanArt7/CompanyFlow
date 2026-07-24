<script setup lang="ts">
import { Building2, Calendar, Monitor, Clock, Users, Headphones } from 'lucide-vue-next'
import type { Feature } from './type'

const features: Feature[] = [
  {
    icon: Building2,
    title: 'Gestion des salles',
    description: "Visualisez et gérez toutes vos salles de réunion en temps réel. Disponibilité, capacité, équipements — tout est centralisé sur une interface intuitive.",
  },
  {
    icon: Calendar,
    title: 'Réservations intelligentes',
    description: "Un système de réservation qui détecte automatiquement les conflits et propose des créneaux optimaux. Fini les doubles réservations.",
  },
  {
    icon: Monitor,
    title: 'Gestion des équipements',
    description: "Suivez votre parc matériel : vidéoprojecteurs, ordinateurs, imprimantes. Attribuez, réservez et maintenez vos équipements sans effort.",
  },
  {
    icon: Clock,
    title: 'Tableau de bord',
    description: "Un dashboard analytique complet avec des indicateurs clés, l'occupation des salles, les statistiques d'utilisation et les tendances.",
  },
  {
    icon: Users,
    title: 'Gestion des utilisateurs',
    description: "Gérez les rôles, les permissions et les équipes. Chaque collaborateur accède uniquement aux ressources dont il a besoin.",
  },
  {
    icon: Headphones,
    title: 'Support technique',
    description: "Une équipe dédiée disponible 7j/7 pour vous accompagner. Assistance prioritaire, base de connaissances et onboarding personnalisé.",
  },
]
</script>

<template>
  <section
    id="features"
    style="scroll-margin-top: 80px;"
    class="w-full px-6 md:px-10 py-16 md:py-24"
  >
    <!-- En-tête de section -->
    <div class="text-center max-w-2xl mx-auto mb-10 md:mb-16">
      <span class="text-primary-light text-sm font-semibold tracking-wide uppercase">
        Plateforme
      </span>
      <h2 class="text-foreground text-2xl sm:text-3xl md:text-4xl font-bold mt-3">
        Tout ce dont vous avez besoin
      </h2>
      <p class="text-muted text-base md:text-lg mt-4 px-2">
        Une suite complète d'outils pour gérer votre entreprise avec élégance et efficacité.
      </p>
    </div>

    <!-- Grille des fonctionnalités -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
      <MarketingFeatureCard
        v-for="feature in features"
        :key="feature.title"
        :icon="feature.icon"
        :title="feature.title"
        :description="feature.description"
      />
    </div>
  </section>
</template>