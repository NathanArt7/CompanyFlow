import type { Component } from 'vue'

export interface StatCardData {
  icon: Component
  iconBg: string
  label: string
  value: string | number
  trend?: {
    value: string
    positive: boolean
  }
  subtext?: string
}

export type ReservationStatus = 'confirmed' | 'pending' | 'cancelled'

export interface UpcomingReservation {
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
}

export interface ActivityItem {
  icon: Component
  iconBg: string
  title: string
  description: string
  time: string
}

export interface RoomOccupancy {
  name: string
  percentage: number
}

export interface TodoItem {
  icon: Component
  iconColor: string
  label: string
  actionLabel: string
  to: string
}

// --- Réponses API brutes ---

export interface RawReservationUser {
  id: number
  nom: string
  prenom: string
  email: string
}

export interface RawReservationRoom {
  id: number
  nom: string
  code: string
  capacite: number
  localisation: string
  statut: string
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
  cancellation_reason: string | null
  cancelled_by: number | null
  created_at: string
  updated_at: string
  deleted_at: string | null
  user: RawReservationUser | null
  room: RawReservationRoom | null
  equipments: unknown[]
  cancelledBy: RawReservationUser | null
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  total: number
  per_page: number
  last_page: number
}

export interface DashboardStats {
  reservations_today: number
  rooms: { available: number, total: number }
  active_users: number
  equipments: { available: number, total: number }
}

export interface DashboardAlerts {
  equipments_in_maintenance: number
  unactivated_users: number
  rooms_in_maintenance: number
}

export interface WeeklyAvailabilitySlot {
  debut: string
  fin: string
  statut: 'LIBRE' | 'OCCUPE'
}

export interface WeeklyAvailabilityRoom {
  id: number
  nom: string
  statut: string
  creneaux: WeeklyAvailabilitySlot[]
}

export interface WeeklyAvailabilityDay {
  date: string
  ouvert: boolean
  salles: WeeklyAvailabilityRoom[]
}

export interface WeeklyAvailabilityResponse {
  jours: WeeklyAvailabilityDay[]
}