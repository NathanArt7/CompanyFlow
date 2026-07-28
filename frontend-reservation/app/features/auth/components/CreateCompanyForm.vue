<script setup lang="ts">
import { Building2, Building, Phone, MapPin, Globe, UploadCloud, X } from 'lucide-vue-next'
import { ref } from 'vue'
import { useCompanyService } from '~/features/onboarding/services/company.service'
import type { ApiError } from '~/composables/useApi'

const companyService = useCompanyService()
const companyStore = useCompanyStore()

const form = ref({
  companyName: '',
  phone: '',
  address: '',
  website: '',
})

const logoFile = ref<File | null>(null)
const logoPreview = ref<string | null>(null)
const isDragging = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')

const fileInput = ref<HTMLInputElement | null>(null)

const handleFileSelect = (file: File | undefined) => {
  if (!file) return

  const validTypes = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp']
  if (!validTypes.includes(file.type)) {
    errorMessage.value = 'Format non supporté. Utilisez PNG, JPG, SVG ou WEBP.'
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    errorMessage.value = 'Le fichier dépasse 2 Mo.'
    return
  }

  errorMessage.value = ''
  logoFile.value = file
  logoPreview.value = URL.createObjectURL(file)
}

const onFileInputChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  handleFileSelect(target.files?.[0])
}

const onDrop = (e: DragEvent) => {
  isDragging.value = false
  handleFileSelect(e.dataTransfer?.files?.[0])
}

const removeLogo = () => {
  logoFile.value = null
  logoPreview.value = null
  if (fileInput.value) fileInput.value.value = ''
}

const handleSubmit = async () => {
  errorMessage.value = ''

  if (!form.value.companyName || !form.value.phone || !form.value.address) {
    errorMessage.value = 'Merci de remplir les champs obligatoires.'
    return
  }

  isSubmitting.value = true
  try {
    const payload = new FormData()
    payload.append('nom', form.value.companyName)
    payload.append('telephone', form.value.phone)
    payload.append('adresse', form.value.address)
    if (form.value.website) payload.append('site_web', form.value.website)
    if (logoFile.value) payload.append('logo', logoFile.value)

    const response = await companyService.configure(payload)
    companyStore.setCompany(response.data as { nom?: string, logo?: string })

    await navigateTo('/onboarding/settings')
  } catch (e) {
    const apiError = e as ApiError
    if (apiError.status === 409) {
      await navigateTo('/dashboard')
      return
    }
    errorMessage.value = apiError.message ?? 'Une erreur est survenue. Veuillez réessayer.'
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
        <div class="flex items-center justify-center gap-2 mb-6">
           <Logo />
        </div>

        <!-- Indicateur d'étape -->
        <div class="flex items-center justify-between mb-2">
          <span class="text-primary-light text-xs font-semibold tracking-wide uppercase">
            Étape 1 sur 2
          </span>
          <span class="text-muted text-xs">Configuration</span>
        </div>
        <div class="w-full h-1 bg-border rounded-full overflow-hidden mb-8">
          <div class="w-1/2 h-full bg-primary rounded-full" />
        </div>

        <!-- Titre + description -->
        <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug text-center">
          Configurez votre entreprise
        </h1>
        <p class="text-muted text-sm mt-3 leading-relaxed text-center">
          Quelques informations suffisent pour personnaliser votre espace CompanyFlow.
        </p>

        <!-- Formulaire -->
        <form class="mt-8 space-y-5" @submit.prevent="handleSubmit">
          <div>
            <label for="companyName" class="text-foreground text-sm font-medium block mb-2">
              Nom de l'entreprise
            </label>
            <div class="relative">
              <Building class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
              <input
                id="companyName"
                v-model="form.companyName"
                type="text"
                placeholder="Nom de votre entreprise"
                class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div>
            <label for="phone" class="text-foreground text-sm font-medium block mb-2">
              Téléphone
            </label>
            <div class="relative">
              <Phone class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                placeholder="+228 90 00 00 00"
                class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div>
            <label for="address" class="text-foreground text-sm font-medium block mb-2">
              Adresse
            </label>
            <div class="relative">
              <MapPin class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
              <input
                id="address"
                v-model="form.address"
                type="text"
                placeholder="Totsi, Lomé"
                class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div>
            <label for="website" class="text-foreground text-sm font-medium block mb-2">
              Site web <span class="text-muted font-normal">(optionnel)</span>
            </label>
            <div class="relative">
              <Globe class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
              <input
                id="website"
                v-model="form.website"
                type="url"
                placeholder="https://votre-entreprise.com"
                class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <!-- Upload logo (drag & drop) -->
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">
              Logo de l'entreprise <span class="text-muted font-normal">(optionnel)</span>
            </label>

            <div
              v-if="!logoPreview"
              class="border-2 border-dashed rounded-xl px-4 py-8 text-center cursor-pointer transition-colors"
              :class="isDragging ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/40'"
              @click="fileInput?.click()"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="onDrop"
            >
              <div class="w-10 h-10 rounded-full bg-surface border border-border flex items-center justify-center mx-auto mb-3">
                <UploadCloud class="w-5 h-5 text-muted" />
              </div>
              <p class="text-foreground text-sm">
                Glissez-déposez votre logo ou
                <span class="text-primary-light font-medium">parcourez</span>
              </p>
              <p class="text-muted text-xs mt-1">PNG, JPG, SVG ou WEBP — Max 2 Mo</p>

              <input
                ref="fileInput"
                type="file"
                accept=".png,.jpg,.jpeg,.svg,.webp"
                class="hidden"
                @change="onFileInputChange"
              >
            </div>

            <!-- Prévisualisation -->
            <div v-else class="flex items-center gap-3 border border-border rounded-xl px-4 py-3">
              <img :src="logoPreview" alt="Logo preview" class="w-10 h-10 rounded-lg object-cover">
              <span class="text-foreground text-sm truncate flex-1">{{ logoFile?.name }}</span>
              <button
                type="button"
                class="text-muted hover:text-red-400 transition-colors"
                @click="removeLogo"
              >
                <X class="w-4 h-4" />
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
            {{ isSubmitting ? 'Enregistrement...' : 'Continuer' }}
          </button>
        </form>
      </div>
    </div>

    <p class="relative z-10 text-center text-muted text-xs mt-8">
      © {{ new Date().getFullYear() }} CompanyFlow. Tous droits réservés.
    </p>
  </div>
</template>