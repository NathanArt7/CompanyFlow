import type { Component } from 'vue'

// --- Réponses API brutes ---

export interface RawReservationUser {
  id: number
  nom: string
  prenom: string
  email: string
  // Présent uniquement si l'utilisateur a été supprimé (soft delete) : la relation
  // reste résolue (withTrashed côté backend) pour garder l'historique consultable.
  deleted_at?: string | null
}

export interface RawReservationRoom {
  id: number
  nom: string
  code: string
  capacite: number
  localisation: string
  statut: string
  // Présent uniquement si la salle a été supprimée (soft delete) : la relation
  // reste résolue (withTrashed côté backend) pour garder l'historique consultable.
  deleted_at?: string | null
}

export interface RawReservationEquipment {
  id: number
  nom: string
}

export interface RawReservation {
  id: number
  entreprise_id: number
  room_id: number
  user_id: number
  motif: string
  nombre_participants: number
  date_reservation: string
  heure_debut: string
  heure_fin: string
  statut: 'CONFIRMEE' | 'ANNULEE'
  cancellation_reason: 'USER_REQUEST' | 'ADMIN_DECISION' | 'ROOM_DELETED' | 'USER_DELETED' | null
  created_at: string
  updated_at: string
  deleted_at: string | null
  user: RawReservationUser | null
  room: RawReservationRoom | null
  equipments: RawReservationEquipment[]
  // Le backend éager-charge toujours la relation `cancelledBy`, qui écrase
  // la colonne brute `cancelled_by` (id) dans la sérialisation Eloquent :
  // cette clé contient donc l'utilisateur complet, jamais un simple id.
  cancelled_by: RawReservationUser | null
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  total: number
  per_page: number
  last_page: number
}

export type ReservationCounts = Record<string, number>

export interface ReservationSummary {
  date: string
  reservations: { total: number, confirmed: number, cancelled: number }
  rooms_occupied: number
  equipments: { reserved: number, total: number }
}

// --- Calendrier (WeekStrip) ---

export interface WeekDayData {
  iso: string
  dayNumber: number
  dayName: string
  count: number
  isSelected: boolean
  isToday: boolean
}

export interface MonthDayData {
  iso: string
  dayNumber: number
  isCurrentMonth: boolean
  isSelected: boolean
  isToday: boolean
  count: number | null
}

// --- Résumé de la journée ---

export interface SummaryCard {
  icon: Component
  iconBg: string
  value: number
  label: string
  barColor: string
  percentage: number
  percentageLabel: string
}

// --- Création de réservation ---

export interface AvailableEquipment {
  id: number
  nom: string
  statut: string
  category_id: number | null
  category_nom: string | null
  disponible: boolean
}

export interface CreateReservationPayload {
  room_id: number
  motif: string
  nombre_participants: number
  date_reservation: string
  heure_debut: string
  heure_fin: string
  equipments?: number[]
}

export type UpdateReservationPayload = CreateReservationPayload

// --- Filtres ---

export interface ReservationFilters {
  roomId: number | null
  status: 'CONFIRMEE' | 'ANNULEE' | 'EN_COURS' | 'PASSEE' | null
  userId: number | null
}

// --- Tableau ---

export type ReservationStatus = 'confirmed' | 'ongoing' | 'cancelled' | 'past'

export interface ReservationRow {
  id: string
  time: string
  room: string
  location: string
  event: string
  organizer: {
    name: string
    initials: string
  }
  status: ReservationStatus
  equipmentCount: number
}
