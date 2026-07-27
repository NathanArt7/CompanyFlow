import type { Component } from 'vue'

export interface AvailabilityStat {
  icon: Component
  iconBg: string
  value: number
  label: string
  subtext: string
}

export interface WeekDay {
  date: number
  dayLabel: string // "Lun 19/05"
  isSelected: boolean
}

export type SlotStatus = 'available' | 'reserved' | 'maintenance' | 'out_of_service'

export interface RoomEvent {
  startCol: number // index de la colonne de début (0-based)
  span: number // nombre de colonnes couvertes
  status: SlotStatus
  title?: string
  time?: string
}

export interface AvailabilityRoom {
  id: string
  name: string
  subtitle: string
  icon: Component
  events: RoomEvent[] // événements pour le jour actuellement sélectionné
}

export interface EquipmentAvailability {
  id: string
  name: string
  subtitle: string
  icon: Component
  events: RoomEvent[] // même structure que pour les salles
}