<script setup lang="ts">
import { X, Loader2, Search } from 'lucide-vue-next'
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { RawEquipment } from '../type'
import { useEquipmentService } from '../services/equipment.service'
import type { RawEquipmentCategory } from '~/features/equipment-categories/type'
import { useEquipmentCategoryService } from '~/features/equipment-categories/services/equipment-category.service'
import type { RawRoom } from '~/features/rooms/type'
import { useRoomService } from '~/features/rooms/services/room.service'
import type { RawUser } from '~/features/users/type'
import { useUserService } from '~/features/users/services/user.service'

const props = defineProps<{
  isOpen: boolean
  equipment?: RawEquipment | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawEquipment]
}>()

const equipmentService = useEquipmentService()
const categoryService = useEquipmentCategoryService()
const roomService = useRoomService()
const userService = useUserService()
const toast = useToast()

const isEditing = computed(() => !!props.equipment)

const categories = ref<RawEquipmentCategory[]>([])
const storageRooms = ref<RawRoom[]>([])

const empruntableStates = ['DISPONIBLE', 'OCCUPE', 'EN_PANNE', 'EN_MAINTENANCE', 'HORS_SERVICE'] as const
const nonEmpruntableStates = ['FONCTIONNEL', 'EN_PANNE', 'EN_MAINTENANCE', 'HORS_SERVICE'] as const

const stateLabels: Record<string, string> = {
  DISPONIBLE: 'Disponible',
  OCCUPE: 'Occupé',
  FONCTIONNEL: 'Fonctionnel',
  EN_PANNE: 'En panne',
  EN_MAINTENANCE: 'En maintenance',
  HORS_SERVICE: 'Hors service',
}

const availableStates = computed(() => form.value.usage_type === 'EMPRUNTABLE' ? empruntableStates : nonEmpruntableStates)

const form = ref({
  nom: '',
  code: '',
  marque: '',
  modele: '',
  numero_serie: '',
  category_id: null as number | null,
  usage_type: 'EMPRUNTABLE' as 'EMPRUNTABLE' | 'NON_EMPRUNTABLE',
  etat: 'DISPONIBLE',
  storage_room_id: null as number | null,
  localisation: '',
  assigned_to: null as number | null,
})

const isSubmitting = ref(false)
const errorMessage = ref('')

// Recherche d'utilisateur pour le champ "Assigné à"
const userSearchQuery = ref('')
const userSearchResults = ref<RawUser[]>([])
const isUserSearchOpen = ref(false)
const isUserSearchLoading = ref(false)
const selectedUser = ref<{ id: number, nom: string, prenom: string, email: string } | null>(null)
let userSearchDebounce: ReturnType<typeof setTimeout> | undefined

async function runUserSearch(query: string) {
  isUserSearchLoading.value = true
  try {
    userSearchResults.value = await userService.search(query)
  } catch {
    userSearchResults.value = []
  } finally {
    isUserSearchLoading.value = false
  }
}

function onUserSearchInput() {
  selectedUser.value = null
  form.value.assigned_to = null
  isUserSearchOpen.value = true
  clearTimeout(userSearchDebounce)
  userSearchDebounce = setTimeout(() => runUserSearch(userSearchQuery.value), 300)
}

function selectUser(user: RawUser) {
  selectedUser.value = user
  form.value.assigned_to = user.id
  userSearchQuery.value = `${user.prenom} ${user.nom}`
  isUserSearchOpen.value = false
}

function clearSelectedUser() {
  selectedUser.value = null
  form.value.assigned_to = null
  userSearchQuery.value = ''
}

function closeUserSearch() {
  isUserSearchOpen.value = false
}

onMounted(() => document.addEventListener('click', closeUserSearch))
onUnmounted(() => document.removeEventListener('click', closeUserSearch))

function resetForm() {
  if (props.equipment) {
    form.value = {
      nom: props.equipment.nom,
      code: props.equipment.code,
      marque: props.equipment.marque,
      modele: props.equipment.modele,
      numero_serie: props.equipment.numero_serie ?? '',
      category_id: props.equipment.category?.id ?? null,
      usage_type: props.equipment.usage_type,
      etat: props.equipment.etat,
      storage_room_id: props.equipment.storage_room?.id ?? null,
      localisation: props.equipment.localisation ?? '',
      assigned_to: props.equipment.assigned_user?.id ?? null,
    }
    if (props.equipment.assigned_user) {
      selectedUser.value = props.equipment.assigned_user
      userSearchQuery.value = `${props.equipment.assigned_user.prenom} ${props.equipment.assigned_user.nom}`
    } else {
      selectedUser.value = null
      userSearchQuery.value = ''
    }
  } else {
    form.value = {
      nom: '',
      code: '',
      marque: '',
      modele: '',
      numero_serie: '',
      category_id: null,
      usage_type: 'EMPRUNTABLE',
      etat: 'DISPONIBLE',
      storage_room_id: null,
      localisation: '',
      assigned_to: null,
    }
    selectedUser.value = null
    userSearchQuery.value = ''
  }
  userSearchResults.value = []
  isUserSearchOpen.value = false
  errorMessage.value = ''
}

watch(() => props.isOpen, (open) => {
  if (open) resetForm()
})

watch(() => form.value.usage_type, (type) => {
  if (!availableStates.value.includes(form.value.etat as never)) {
    form.value.etat = type === 'EMPRUNTABLE' ? 'DISPONIBLE' : 'FONCTIONNEL'
  }
  if (type === 'NON_EMPRUNTABLE') {
    form.value.storage_room_id = null
  } else {
    form.value.localisation = ''
    clearSelectedUser()
  }
})

async function handleSubmit() {
  errorMessage.value = ''

  if (!form.value.nom) {
    errorMessage.value = 'Merci de renseigner un nom.'
    return
  }
  if (!form.value.code) {
    errorMessage.value = 'Merci de renseigner un code.'
    return
  }
  if (!form.value.marque) {
    errorMessage.value = 'Merci de renseigner une marque.'
    return
  }
  if (!form.value.modele) {
    errorMessage.value = 'Merci de renseigner un modèle.'
    return
  }
  if (!form.value.category_id) {
    errorMessage.value = 'Merci de sélectionner une catégorie.'
    return
  }
  if (form.value.usage_type === 'EMPRUNTABLE' && !form.value.storage_room_id) {
    errorMessage.value = 'Merci de sélectionner une salle de stockage.'
    return
  }
  if (form.value.usage_type === 'NON_EMPRUNTABLE' && !form.value.localisation) {
    errorMessage.value = 'Merci de renseigner une localisation.'
    return
  }

  const payload = {
    nom: form.value.nom,
    code: form.value.code,
    marque: form.value.marque,
    modele: form.value.modele,
    numero_serie: form.value.numero_serie || null,
    category_id: form.value.category_id,
    usage_type: form.value.usage_type,
    etat: form.value.etat,
    storage_room_id: form.value.usage_type === 'EMPRUNTABLE' ? form.value.storage_room_id : null,
    localisation: form.value.usage_type === 'NON_EMPRUNTABLE' ? form.value.localisation : null,
    assigned_to: form.value.usage_type === 'NON_EMPRUNTABLE' ? form.value.assigned_to : null,
  }

  isSubmitting.value = true
  try {
    const saved = isEditing.value
      ? await equipmentService.update(props.equipment!.id, payload)
      : await equipmentService.create(payload)
    emit('saved', saved)
    emit('close')
    toast.success(isEditing.value ? 'Équipement modifié avec succès.' : 'Équipement ajouté avec succès.')
  } catch (e) {
    errorMessage.value = (e as ApiError).message ?? 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    isSubmitting.value = false
  }
}

onMounted(async () => {
  try {
    categories.value = await categoryService.list()
  } catch {
    categories.value = []
  }
  try {
    storageRooms.value = (await roomService.list()).filter(room => room.type.value === 'STORAGE')
  } catch {
    storageRooms.value = []
  }
})
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
            {{ isEditing ? "Modifier l'équipement" : 'Ajouter un équipement' }}
          </h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Nom</label>
            <input
              v-model="form.nom"
              type="text"
              placeholder="Vidéoprojecteur Epson"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Code</label>
              <input
                v-model="form.code"
                type="text"
                placeholder="EQP-0001"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Catégorie</label>
              <select
                v-model="form.category_id"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
                <option :value="null">Sélectionner</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.nom }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Marque</label>
              <input
                v-model="form.marque"
                type="text"
                placeholder="Epson"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Modèle</label>
              <input
                v-model="form.modele"
                type="text"
                placeholder="EB-X05"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">
              Numéro de série <span class="text-muted font-normal">(optionnel)</span>
            </label>
            <input
              v-model="form.numero_serie"
              type="text"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Type d'usage</label>
              <select
                v-model="form.usage_type"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
                <option value="EMPRUNTABLE">Empruntable</option>
                <option value="NON_EMPRUNTABLE">Non empruntable</option>
              </select>
            </div>
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">État</label>
              <select
                v-model="form.etat"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
                <option v-for="state in availableStates" :key="state" :value="state">
                  {{ stateLabels[state] }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="form.usage_type === 'EMPRUNTABLE'">
            <label class="text-foreground text-sm font-medium block mb-2">Salle de stockage</label>
            <select
              v-model="form.storage_room_id"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
            >
              <option :value="null">Sélectionner</option>
              <option v-for="room in storageRooms" :key="room.id" :value="room.id">
                {{ room.nom }}
              </option>
            </select>
            <p v-if="storageRooms.length === 0" class="text-muted text-xs mt-1">
              Aucune salle de stockage disponible — créez-en une depuis la page Salles.
            </p>
          </div>

          <div v-else class="space-y-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Localisation</label>
              <input
                v-model="form.localisation"
                type="text"
                placeholder="Bureau du Comptable"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>

            <div class="relative" @click.stop>
              <label class="text-foreground text-sm font-medium block mb-2">
                Assigné à <span class="text-muted font-normal">(optionnel)</span>
              </label>
              <div class="relative">
                <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  v-model="userSearchQuery"
                  type="text"
                  placeholder="Rechercher un utilisateur..."
                  class="w-full bg-background border border-border rounded-lg pl-10 pr-9 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
                  @input="onUserSearchInput"
                  @focus="isUserSearchOpen = true"
                >
                <button
                  v-if="selectedUser"
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors"
                  @click="clearSelectedUser"
                >
                  <X class="w-4 h-4" />
                </button>
              </div>

              <div
                v-if="isUserSearchOpen && !selectedUser"
                class="absolute left-0 right-0 top-full mt-1 z-10 max-h-48 overflow-y-auto bg-surface border border-border rounded-lg shadow-lg"
              >
                <p v-if="isUserSearchLoading" class="text-muted text-xs px-3 py-2.5">
                  Recherche…
                </p>
                <p v-else-if="userSearchResults.length === 0" class="text-muted text-xs px-3 py-2.5">
                  Aucun utilisateur trouvé.
                </p>
                <button
                  v-for="user in userSearchResults"
                  v-else
                  :key="user.id"
                  type="button"
                  class="w-full text-left px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                  @click="selectUser(user)"
                >
                  {{ user.prenom }} {{ user.nom }}
                  <span class="text-muted text-xs block">{{ user.email }}</span>
                </button>
              </div>
            </div>
          </div>

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
              {{ isSubmitting ? 'Enregistrement...' : (isEditing ? 'Enregistrer' : "Ajouter l'équipement") }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
