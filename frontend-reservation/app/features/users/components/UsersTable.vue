<script setup lang="ts">
import { Pencil, ChevronLeft, ChevronRight, UserX, UserCheck, Trash2 } from 'lucide-vue-next'
import { computed } from 'vue'
import type { AppUser, UserRole, UserStatus } from '../types'
import type { RawUser, RoleName } from '../type'
import { useAuthStore } from '~/stores/auth.store'

const props = defineProps<{
  users: RawUser[]
  isLoading: boolean
  error: string | null
  page: number
  perPage: number
  total: number
  lastPage: number
}>()

const emit = defineEmits<{
  'update:page': [number]
  'update:perPage': [number]
  edit: [RawUser]
  'toggle-status': [RawUser]
  delete: [RawUser]
}>()

const authStore = useAuthStore()

const config = useRuntimeConfig()
function photoUrl(photo: string | null): string | null {
  if (!photo) return null
  return `${config.public.apiBase.replace(/\/api\/?$/, '')}/storage/${photo}`
}

const roleMap: Record<RoleName, UserRole> = {
  Super_Administrateur: 'super_admin',
  Administrateur: 'admin',
  Super_Employe: 'super_employee',
  Employe: 'employee',
  Technicien: 'technician',
}

const avatarColors = ['bg-primary/20 text-primary-light', 'bg-blue-500/20 text-blue-400', 'bg-cyan-500/20 text-cyan-400', 'bg-green-500/20 text-green-500', 'bg-orange-500/20 text-orange-500']

function mapUser(u: RawUser): AppUser & { raw: RawUser } {
  const status: UserStatus = u.actif ? 'active' : 'disabled'
  return {
    id: String(u.id),
    name: `${u.prenom} ${u.nom}`,
    email: u.email,
    initials: `${u.prenom.charAt(0)}${u.nom.charAt(0)}`.toUpperCase(),
    avatarBg: avatarColors[u.id % avatarColors.length]!,
    role: roleMap[u.role],
    status,
    createdAt: new Date(u.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }),
    raw: u,
  }
}

const rows = computed(() => props.users.map(mapUser))

function canManage(target: RawUser): boolean {
  // La gestion du compte Super Administrateur (rôle unique, non réassignable)
  // se fait en dehors de cette table.
  if (target.role === 'Super_Administrateur') return false

  const role = authStore.user?.role
  if (role === 'Super_Administrateur') return true
  if (role === 'Administrateur') return target.role !== 'Administrateur' && target.role !== 'Super_Administrateur'
  return false
}

function canToggleStatus(target: RawUser): boolean {
  return canManage(target) && target.id !== authStore.user?.id
}

function canDelete(target: RawUser): boolean {
  return canManage(target) && target.id !== authStore.user?.id
}

const pageSize = computed({
  get: () => String(props.perPage),
  set: (value: string) => emit('update:perPage', Number(value)),
})

const rangeStart = computed(() => props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1)
const rangeEnd = computed(() => Math.min(props.page * props.perPage, props.total))
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <SkeletonTable v-if="isLoading" :rows="perPage > 8 ? 8 : perPage" :columns="5" />
    <p v-else-if="error" class="text-red-400 text-xs p-5">
      {{ error }}
    </p>
    <p v-else-if="rows.length === 0" class="text-muted text-xs p-5">
      Aucun utilisateur trouvé.
    </p>

    <template v-else>
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-muted text-xs border-b border-border">
              <th class="font-medium py-3 pl-5 pr-4">Utilisateur</th>
              <th class="font-medium py-3 pr-4">Rôle</th>
              <th class="font-medium py-3 pr-4">Statut</th>
              <th class="font-medium py-3 pr-4">Créé le</th>
              <th class="font-medium py-3 pr-5">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="user in rows"
              :key="user.id"
              class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
            >
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <img v-if="photoUrl(user.raw.photo)" :src="photoUrl(user.raw.photo)!" class="w-9 h-9 rounded-full object-cover shrink-0" alt="">
                  <div v-else class="w-9 h-9 rounded-full flex items-center justify-center shrink-0" :class="user.avatarBg">
                    <span class="text-xs font-semibold">{{ user.initials }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="text-foreground font-medium whitespace-nowrap">{{ user.name }}</p>
                    <p class="text-muted text-xs whitespace-nowrap">{{ user.email }}</p>
                  </div>
                </div>
              </td>
              <td class="py-4 pr-4">
                <RoleBadge :role="user.role" />
              </td>
              <td class="py-4 pr-4">
                <UserStatusBadge :status="user.status" />
              </td>
              <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ user.createdAt }}</td>
              <td class="py-4 pr-5">
                <div class="flex items-center gap-2">
                  <button
                    v-if="canManage(user.raw)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
                    title="Modifier"
                    @click="emit('edit', user.raw)"
                  >
                    <Pencil class="w-3.5 h-3.5" />
                  </button>
                  <button
                    v-if="canToggleStatus(user.raw)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border transition-colors"
                    :class="user.raw.actif ? 'text-muted hover:text-red-400 hover:border-red-400/40' : 'text-muted hover:text-green-500 hover:border-green-500/40'"
                    :title="user.raw.actif ? 'Désactiver' : 'activer'"
                    @click="emit('toggle-status', user.raw)"
                  >
                    <UserX v-if="user.raw.actif" class="w-3.5 h-3.5" />
                    <UserCheck v-else class="w-3.5 h-3.5" />
                  </button>
                  <button
                    v-if="canDelete(user.raw)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-red-400 hover:border-red-400/40 transition-colors"
                    title="Supprimer définitivement"
                    @click="emit('delete', user.raw)"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards mobile / tablette -->
      <div class="lg:hidden divide-y divide-border">
        <div v-for="user in rows" :key="user.id" class="p-4">
          <div class="flex items-center gap-3 mb-2">
            <img v-if="photoUrl(user.raw.photo)" :src="photoUrl(user.raw.photo)!" class="w-9 h-9 rounded-full object-cover shrink-0" alt="">
            <div v-else class="w-9 h-9 rounded-full flex items-center justify-center shrink-0" :class="user.avatarBg">
              <span class="text-xs font-semibold">{{ user.initials }}</span>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-foreground font-medium truncate">{{ user.name }}</p>
              <p class="text-muted text-xs truncate">{{ user.email }}</p>
            </div>
          </div>
          <div class="flex items-center justify-between mb-1">
            <RoleBadge :role="user.role" />
            <UserStatusBadge :status="user.status" />
          </div>
          <p class="text-muted text-xs">Créé le {{ user.createdAt }}</p>
          <div class="flex items-center gap-2 mt-3">
            <button
              v-if="canManage(user.raw)"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
              title="Modifier"
              @click="emit('edit', user.raw)"
            >
              <Pencil class="w-3.5 h-3.5" />
            </button>
            <button
              v-if="canToggleStatus(user.raw)"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border transition-colors"
              :class="user.raw.actif ? 'text-muted hover:text-red-400 hover:border-red-400/40' : 'text-muted hover:text-green-500 hover:border-green-500/40'"
              :title="user.raw.actif ? 'Désactiver' : 'Activer'"
              @click="emit('toggle-status', user.raw)"
            >
              <UserX v-if="user.raw.actif" class="w-3.5 h-3.5" />
              <UserCheck v-else class="w-3.5 h-3.5" />
            </button>
            <button
              v-if="canDelete(user.raw)"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-red-400 hover:border-red-400/40 transition-colors"
              title="Supprimer définitivement"
              @click="emit('delete', user.raw)"
            >
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
        <p class="text-muted text-xs">
          Affichage de {{ rangeStart }} à {{ rangeEnd }} sur {{ total }} utilisateurs
        </p>

        <div class="flex items-center gap-2">
          <button
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            :disabled="page === 1"
            @click="emit('update:page', page - 1)"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>

          <button
            v-for="p in lastPage"
            :key="p"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
            :class="page === p ? 'bg-primary text-white' : 'text-muted hover:text-foreground hover:bg-border/50'"
            @click="emit('update:page', p)"
          >
            {{ p }}
          </button>

          <button
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            :disabled="page === lastPage"
            @click="emit('update:page', page + 1)"
          >
            <ChevronRight class="w-4 h-4" />
          </button>

          <select
            v-model="pageSize"
            class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors ml-1"
          >
            <option value="10">10 par page</option>
            <option value="20">20 par page</option>
            <option value="50">50 par page</option>
          </select>
        </div>
      </div>
    </template>
  </div>
</template>
