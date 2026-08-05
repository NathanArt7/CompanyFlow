<script setup lang="ts">
import { Pencil, Building2 } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import type { RawEntreprise } from '../type'
import { useEntrepriseService } from '../services/entreprise.service'

const entrepriseService = useEntrepriseService()
const authStore = useAuthStore()

const entreprise = ref<RawEntreprise | null>(null)
const isLoading = ref(true)
const error = ref<string | null>(null)
const isModalOpen = ref(false)

const canEdit = computed(() => authStore.permissions.includes('configurer_systeme'))

const config = useRuntimeConfig()
const logoUrl = computed(() => {
  if (!entreprise.value?.logo) return null
  return `${config.public.apiBase.replace(/\/api\/?$/, '')}/storage/${entreprise.value.logo}`
})

async function load() {
  isLoading.value = true
  error.value = null
  try {
    entreprise.value = await entrepriseService.get()
  } catch {
    error.value = "Impossible de charger les informations de l'entreprise."
  } finally {
    isLoading.value = false
  }
}

function onSaved(updated: RawEntreprise) {
  entreprise.value = updated
  isModalOpen.value = false
}

onMounted(load)
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-center justify-between mb-1">
      <h2 class="text-foreground font-semibold">Informations de l'entreprise</h2>
      <button
        v-if="entreprise && canEdit"
        class="flex items-center gap-2 bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors"
        @click="isModalOpen = true"
      >
        <Pencil class="w-3.5 h-3.5" />
        Modifier
      </button>
    </div>
    <p class="text-muted text-sm mb-6">Modifiez les informations générales de votre entreprise.</p>

    <p v-if="isLoading" class="text-muted text-xs">
      Chargement…
    </p>
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>

    <div v-else-if="entreprise" class="flex flex-col md:flex-row gap-8">
      <!-- Logo -->
      <div class="flex flex-col items-center gap-3 shrink-0">
        <div class="w-28 h-28 rounded-xl border border-border bg-background flex items-center justify-center overflow-hidden">
          <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="w-full h-full object-cover">
          <Building2 v-else class="w-10 h-10 text-primary-light" />
        </div>
        <p class="text-muted text-xs text-center">PNG, JPG ou SVG. Max 2MB.</p>
      </div>

      <!-- Détails -->
      <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Nom de l'entreprise</span>
          <p class="text-foreground text-sm sm:mt-1">{{ entreprise.nom }}</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Référence</span>
          <p class="text-foreground text-sm sm:mt-1">{{ entreprise.reference }}</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Téléphone</span>
          <p class="text-foreground text-sm sm:mt-1">{{ entreprise.telephone }}</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Site web</span>
          <a
            v-if="entreprise.site_web"
            :href="entreprise.site_web"
            target="_blank"
            rel="noopener noreferrer"
            class="text-primary-light text-sm sm:mt-1 block hover:underline"
          >
            {{ entreprise.site_web }}
          </a>
          <p v-else class="text-foreground text-sm sm:mt-1">—</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Adresse</span>
          <p class="text-foreground text-sm sm:mt-1">{{ entreprise.adresse }}</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Statut</span>
          <span
            class="inline-block mt-0.5 sm:mt-1 text-xs font-medium px-2 py-0.5 rounded-full"
            :class="entreprise.statut === 'Active' ? 'text-green-500 bg-green-500/10' : 'text-orange-500 bg-orange-500/10'"
          >
            {{ entreprise.statut }}
          </span>
        </div>
      </div>
    </div>

    <EditCompanyInfoModal
      :is-open="isModalOpen"
      :entreprise="entreprise"
      @close="isModalOpen = false"
      @saved="onSaved"
    />
  </div>
</template>
