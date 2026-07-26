<script setup lang="ts">
import { Pencil, MoreHorizontal, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { ref } from 'vue'
import type { AppUser } from '../types'

const users: AppUser[] = [
  { id: '1', name: 'Marc Lorem', email: 'marc.lorem@company.com', initials: 'ML', avatarBg: 'bg-primary/20 text-primary-light', role: 'super_admin', status: 'active', lastLogin: "Aujourd'hui, 09:15", createdAt: '12 mai 2026' },
  { id: '2', name: 'Sarah Johnson', email: 'sarah.johnson@company.com', initials: 'SJ', avatarBg: 'bg-blue-500/20 text-blue-400', role: 'admin', status: 'active', lastLogin: 'Hier, 16:42', createdAt: '18 mai 2026' },
  { id: '3', name: 'David Tchalla', email: 'david.tchalla@company.com', initials: 'DT', avatarBg: 'bg-cyan-500/20 text-cyan-400', role: 'super_employee', status: 'active', lastLogin: "Aujourd'hui, 08:30", createdAt: '22 mai 2026' },
  { id: '4', name: 'Amina Mohamed', email: 'amina.mohamed@company.com', initials: 'AM', avatarBg: 'bg-green-500/20 text-green-500', role: 'employee', status: 'active', lastLogin: 'Hier, 11:05', createdAt: '25 mai 2026' },
  { id: '5', name: 'Yann Legrand', email: 'yann.legrand@company.com', initials: 'YL', avatarBg: 'bg-orange-500/20 text-orange-500', role: 'technician', status: 'pending', lastLogin: '—', createdAt: '23 juillet 2026' },
  { id: '6', name: 'Kevin Bernard', email: 'kevin.bernard@company.com', initials: 'KB', avatarBg: 'bg-green-500/20 text-green-500', role: 'employee', status: 'disabled', lastLogin: '15 juil. 2026', createdAt: '10 mai 2026' },
  { id: '7', name: "Isabelle N'Guessan", email: 'isabelle.nguessan@company.com', initials: 'IN', avatarBg: 'bg-green-500/20 text-green-500', role: 'employee', status: 'suspended', lastLogin: '2 juil. 2026', createdAt: '5 mai 2026' },
]

const currentPage = ref(1)
const totalPages = 9
const totalResults = 86
const pageSize = ref('10')
const visiblePages = [1, 2, 3]
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-muted text-xs border-b border-border">
            <th class="font-medium py-3 pl-5 pr-4">Utilisateur</th>
            <th class="font-medium py-3 pr-4">Rôle</th>
            <th class="font-medium py-3 pr-4">Statut</th>
            <th class="font-medium py-3 pr-4">Dernière connexion</th>
            <th class="font-medium py-3 pr-4">Créé le</th>
            <th class="font-medium py-3 pr-5">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
          >
            <td class="py-4 pl-5 pr-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0" :class="user.avatarBg">
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
            <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ user.lastLogin }}</td>
            <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ user.createdAt }}</td>
            <td class="py-4 pr-5">
              <div class="flex items-center gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
                  <Pencil class="w-3.5 h-3.5" />
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
                  <MoreHorizontal class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Cards mobile / tablette -->
    <div class="lg:hidden divide-y divide-border">
      <div v-for="user in users" :key="user.id" class="p-4">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0" :class="user.avatarBg">
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
        <p class="text-muted text-xs">Dernière connexion : {{ user.lastLogin }}</p>
        <p class="text-muted text-xs">Créé le {{ user.createdAt }}</p>
        <div class="flex items-center gap-2 mt-3">
          <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
            <Pencil class="w-3.5 h-3.5" />
          </button>
          <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
            <MoreHorizontal class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
      <p class="text-muted text-xs">
        Affichage de 1 à 7 sur {{ totalResults }} utilisateurs
      </p>

      <div class="flex items-center gap-2">
        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          <ChevronLeft class="w-4 h-4" />
        </button>

        <button
          v-for="page in visiblePages"
          :key="page"
          class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
          :class="currentPage === page ? 'bg-primary text-white' : 'text-muted hover:text-foreground hover:bg-border/50'"
          @click="currentPage = page"
        >
          {{ page }}
        </button>

        <span class="text-muted text-sm px-1">...</span>

        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
          :class="currentPage === totalPages ? 'bg-primary text-white' : 'text-muted hover:text-foreground hover:bg-border/50'"
          @click="currentPage = totalPages"
        >
          {{ totalPages }}
        </button>

        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          <ChevronRight class="w-4 h-4" />
        </button>

        <select v-model="pageSize" class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors ml-1">
          <option value="10">10 par page</option>
          <option value="20">20 par page</option>
          <option value="50">50 par page</option>
        </select>
      </div>
    </div>
  </div>
</template>