<script setup lang="ts">
import { Pencil, MoreHorizontal, ChevronLeft, ChevronRight, MoreHorizontal as Dots } from 'lucide-vue-next'
import { ref } from 'vue'
import type { Equipment } from '../types'

const equipments: Equipment[] = [
  { id: '1', name: 'Vidéoprojecteur Epson', code: 'EQP-0001', category: 'Vidéoprojecteurs', usageType: 'borrowable', location: 'Salle de stockage 1', state: 'available', assignedTo: null },
  { id: '2', name: 'Ordinateur portable Dell', code: 'EQP-0002', category: 'Ordinateurs', usageType: 'borrowable', location: 'Salle de stockage 1', state: 'occupied', assignedTo: null },
  { id: '3', name: 'Microphone sans fil', code: 'EQP-0003', category: 'Audio', usageType: 'borrowable', location: 'Salle de stockage 2', state: 'maintenance', assignedTo: null },
  { id: '4', name: 'Écran de projection 120"', code: 'EQP-0004', category: 'Accessoires', usageType: 'fixed', location: 'Salle de réunion A', state: 'functional', assignedTo: 'Amina Mohamed' },
  { id: '5', name: 'Ordinateur fixe HP', code: 'EQP-0005', category: 'Ordinateurs', usageType: 'fixed', location: 'Bureau IT', state: 'broken', assignedTo: null },
  { id: '6', name: 'Enceinte Bluetooth', code: 'EQP-0006', category: 'Audio', usageType: 'borrowable', location: 'Salle de stockage 2', state: 'available', assignedTo: null },
  { id: '7', name: 'Caméra de conférence', code: 'EQP-0007', category: 'Visioconférence', usageType: 'fixed', location: 'Salle de réunion B', state: 'functional', assignedTo: null },
  { id: '8', name: 'Tableau blanc mobile', code: 'EQP-0008', category: 'Accessoires', usageType: 'fixed', location: 'Salle de formation', state: 'out_of_service', assignedTo: null },
]

const currentPage = ref(1)
const totalPages = 6
const totalResults = 48
const pageSize = ref('10')

const visiblePages = [1, 2, 3]
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-muted text-xs border-b border-border">
            <th class="font-medium py-3 pl-5 pr-4">Nom</th>
            <th class="font-medium py-3 pr-4">Code</th>
            <th class="font-medium py-3 pr-4">Catégorie</th>
            <th class="font-medium py-3 pr-4">Type d'usage</th>
            <th class="font-medium py-3 pr-4">Localisation</th>
            <th class="font-medium py-3 pr-4">État</th>
            <th class="font-medium py-3 pr-4">Assigné à</th>
            <th class="font-medium py-3 pr-5">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="equipment in equipments"
            :key="equipment.id"
            class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
          >
            <td class="py-4 pl-5 pr-4 text-foreground font-medium whitespace-nowrap">{{ equipment.name }}</td>
            <td class="py-4 pr-4 text-muted whitespace-nowrap">{{ equipment.code }}</td>
            <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ equipment.category }}</td>
            <td class="py-4 pr-4">
              <UsageTypeBadge :type="equipment.usageType" />
            </td>
            <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ equipment.location }}</td>
            <td class="py-4 pr-4">
              <EquipmentStateBadge :state="equipment.state" />
            </td>
            <td class="py-4 pr-4 text-foreground whitespace-nowrap">
              {{ equipment.assignedTo ?? '—' }}
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

    <!-- Cards mobile / tablette -->
    <div class="lg:hidden divide-y divide-border">
      <div v-for="equipment in equipments" :key="equipment.id" class="p-4">
        <div class="flex items-center justify-between mb-2">
          <div>
            <p class="text-foreground font-medium">{{ equipment.name }}</p>
            <p class="text-muted text-xs">Code: {{ equipment.code }}</p>
          </div>
          <UsageTypeBadge :type="equipment.usageType" />
        </div>
        <p class="text-muted text-xs mb-1">{{ equipment.category }} · {{ equipment.location }}</p>
        <div class="flex items-center justify-between mt-2">
          <EquipmentStateBadge :state="equipment.state" />
          <span v-if="equipment.assignedTo" class="text-muted text-xs">{{ equipment.assignedTo }}</span>
        </div>
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
        Affichage de 1 à 8 sur {{ totalResults }} équipements
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