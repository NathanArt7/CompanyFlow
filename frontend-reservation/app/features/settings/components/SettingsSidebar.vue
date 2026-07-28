<script setup lang="ts">
import { Settings, Clock, Bell, Shield, Cloud, Info } from 'lucide-vue-next'
import { ref } from 'vue'
import type { SettingsTab } from '../types'

const tabs: SettingsTab[] = [
  { id: 'general', icon: Settings, label: 'Général', subtitle: "Informations de l'entreprise" },
  { id: 'hours', icon: Clock, label: 'Horaires de service', subtitle: "Définir les heures d'ouverture" },
  { id: 'notifications', icon: Bell, label: 'Notifications', subtitle: 'Préférences de notification' },
  { id: 'security', icon: Shield, label: 'Sécurité', subtitle: 'Mots de passe et sessions' },
  { id: 'backup', icon: Cloud, label: 'Sauvegarde', subtitle: 'Sauvegardes et restauration' },
  { id: 'about', icon: Info, label: 'À propos', subtitle: "Informations sur l'application" },
]

const activeTab = ref('general')
</script>

<template>
  <nav class="space-y-1">
    <button
      v-for="tab in tabs"
      :key="tab.id"
      class="w-full flex items-start gap-3 px-4 py-3 rounded-lg text-left transition-colors"
      :class="activeTab === tab.id ? 'bg-primary/15' : 'hover:bg-border/30'"
      @click="activeTab = tab.id"
    >
      <component
        :is="tab.icon"
        class="w-4.5 h-4.5 mt-0.5 shrink-0"
        :class="activeTab === tab.id ? 'text-primary-light' : 'text-muted'"
      />
      <div class="min-w-0">
        <p class="text-sm font-medium" :class="activeTab === tab.id ? 'text-primary-light' : 'text-foreground'">
          {{ tab.label }}
        </p>
        <p class="text-muted text-xs truncate">{{ tab.subtitle }}</p>
      </div>
    </button>
  </nav>
</template>