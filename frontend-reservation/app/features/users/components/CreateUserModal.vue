<script setup lang="ts">
import { X, Loader2 } from 'lucide-vue-next'
import { ref, computed, watch, onMounted } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { RawRole, RawUser } from '../type'
import { useUserService } from '../services/user.service'

const props = defineProps<{
  isOpen: boolean
  user?: RawUser | null
}>()

const emit = defineEmits<{
  close: []
  saved: []
}>()

const userService = useUserService()

const isEditing = computed(() => !!props.user)

const roles = ref<RawRole[]>([])
const rolesError = ref(false)

const form = ref({
  nom: '',
  prenom: '',
  email: '',
  roleId: null as number | null,
})

const isSubmitting = ref(false)
const errorMessage = ref('')

function resetForm() {
  if (props.user) {
    const matchedRole = roles.value.find(r => r.nom === props.user!.role)
    form.value = {
      nom: props.user.nom,
      prenom: props.user.prenom,
      email: props.user.email,
      roleId: matchedRole?.id ?? null,
    }
  } else {
    form.value = { nom: '', prenom: '', email: '', roleId: null }
  }
  errorMessage.value = ''
}

async function loadRoles() {
  try {
    roles.value = await userService.listRoles()
  } catch {
    rolesError.value = true
  }
}

watch(() => props.isOpen, async (open) => {
  if (open) {
    if (roles.value.length === 0 && !rolesError.value) await loadRoles()
    resetForm()
  }
})

onMounted(loadRoles)

async function handleSubmit() {
  errorMessage.value = ''

  if (!form.value.nom) {
    errorMessage.value = 'Merci de renseigner un nom.'
    return
  }
  if (!form.value.prenom) {
    errorMessage.value = 'Merci de renseigner un prénom.'
    return
  }
  if (!form.value.email) {
    errorMessage.value = 'Merci de renseigner un email.'
    return
  }
  if (!form.value.roleId) {
    errorMessage.value = 'Merci de sélectionner un rôle.'
    return
  }

  const payload = {
    nom: form.value.nom,
    prenom: form.value.prenom,
    email: form.value.email,
    role_id: form.value.roleId,
  }

  isSubmitting.value = true
  try {
    if (isEditing.value) {
      await userService.update(props.user!.id, payload)
    } else {
      await userService.create(payload)
    }
    emit('saved')
    emit('close')
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
      <div class="bg-surface border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">
            {{ isEditing ? "Modifier l'utilisateur" : 'Ajouter un utilisateur' }}
          </h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Prénom</label>
              <input
                v-model="form.prenom"
                type="text"
                placeholder="Jean"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Nom</label>
              <input
                v-model="form.nom"
                type="text"
                placeholder="Dupont"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="jean.dupont@entreprise.com"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Rôle</label>
            <select
              v-model="form.roleId"
              :disabled="rolesError"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors disabled:opacity-60"
            >
              <option :value="null">Sélectionner</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.nom.replace('_', ' ') }}
              </option>
            </select>
            <p v-if="rolesError" class="text-muted text-xs mt-1">
              Impossible de charger la liste des rôles.
            </p>
          </div>

          <p v-if="!isEditing" class="text-muted text-xs">
            Un e-mail d'activation sera envoyé à cet utilisateur pour qu'il définisse son mot de passe.
          </p>

          <p v-if="errorMessage" class="text-red-400 text-sm">
            {{ errorMessage }}
          </p>

          <div class="flex items-center gap-3 pt-2">
            <button
              type="button"
              class="flex-1 bg-background hover:bg-border text-foreground font-medium py-2.5 rounded-lg border border-border transition-colors"
              @click="emit('close')"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg transition-colors"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              {{ isSubmitting ? 'Enregistrement...' : (isEditing ? 'Enregistrer' : "Ajouter l'utilisateur") }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
