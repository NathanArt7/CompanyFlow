<script setup lang="ts">
import { Users, Pencil, MoreHorizontal, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { ref } from 'vue'
import type { Room } from '../types'

const rooms: Room[] = [
  { id: '1', name: 'Salle de réunion A', code: 'SAL-A-001', type: 'reunion', capacity: 12, location: 'Bâtiment A, 2ᵉ étage', status: 'available' },
  { id: '2', name: 'Salle de réunion B', code: 'SAL-B-002', type: 'reunion', capacity: 8, location: 'Bâtiment A, 2ᵉ étage', status: 'occupied' },
  { id: '3', name: 'Salle de conférence', code: 'SAL-C-003', type: 'reunion', capacity: 20, location: 'Bâtiment B, 1ᵉʳ étage', status: 'available' },
  { id: '4', name: 'Salle de formation', code: 'SAL-D-004', type: 'reunion', capacity: 16, location: 'Bâtiment B, 1ᵉʳ étage', status: 'maintenance' },
  { id: '5', name: 'Salle de créativité', code: 'SAL-E-005', type: 'reunion', capacity: 6, location: 'Bâtiment A, 3ᵉ étage', status: 'available' },
  { id: '6', name: 'Salle de stockage IT', code: 'STO-IT-001', type: 'stockage', capacity: null, location: 'Bâtiment C, RDC', status: 'available' },
]

const currentPage = ref(1)
const totalPages = 3
const totalResults = 24
const pageSize = ref('10')
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <!-- Tableau desktop -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-muted text-xs border-b border-border">
            <th class="font-medium py-3 pl-5 pr-4">Salle</th>
            <th class="font-medium py-3 pr-4">Type</th>
            <th class="font-medium py-3 pr-4">Capacité</th>
            <th class="font-medium py-3 pr-4">Localisation</th>
            <th class="font-medium py-3 pr-4">Statut</th>
            <th class="font-medium py-3 pr-5">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="room in rooms"
            :key="room.id"
            class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
          >
            <td class="py-4 pl-5 pr-4">
              <p class="text-foreground font-medium">{{ room.name }}</p>
              <p class="text-muted text-xs">Code: {{ room.code }}</p>
            </td>
            <td class="py-4 pr-4">
              <RoomTypeBadge :type="room.type" />
            </td>
            <td class="py-4 pr-4">
              <div v-if="room.capacity" class="flex items-center gap-1.5 text-foreground">
                <Users class="w-4 h-4 text-muted" />
                {{ room.capacity }} places
              </div>
              <span v-else class="text-muted">—</span>
            </td>
            <td class="py-4 pr-4 text-foreground">{{ room.location }}</td>
            <td class="py-4 pr-4">
              <RoomStatusBadge :status="room.status" />
            </td>
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

    <!-- Cards mobile -->
    <div class="md:hidden divide-y divide-border">
      <div v-for="room in rooms" :key="room.id" class="p-4">
        <div class="flex items-center justify-between mb-2">
          <div>
            <p class="text-foreground font-medium">{{ room.name }}</p>
            <p class="text-muted text-xs">Code: {{ room.code }}</p>
          </div>
          <RoomTypeBadge :type="room.type" />
        </div>
        <div class="flex items-center justify-between text-sm mb-2">
          <span class="text-muted">{{ room.location }}</span>
          <span v-if="room.capacity" class="flex items-center gap-1 text-foreground">
            <Users class="w-3.5 h-3.5 text-muted" />
            {{ room.capacity }} places
          </span>
        </div>
        <div class="flex items-center justify-between">
          <RoomStatusBadge :status="room.status" />
          <div class="flex items-center gap-2">
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
              <Pencil class="w-3.5 h-3.5" />
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
              <MoreHorizontal class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
      <p class="text-muted text-xs">
        Affichage de 1 à 6 sur {{ totalResults }} salles
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
          v-for="page in totalPages"
          :key="page"
          class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
          :class="currentPage === page ? 'bg-primary text-white' : 'text-muted hover:text-foreground hover:bg-border/50'"
          @click="currentPage = page"
        >
          {{ page }}
        </button>

        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
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
  </div>
</template>