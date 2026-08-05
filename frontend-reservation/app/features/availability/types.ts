import type { Component } from 'vue'

export interface AvailabilityStat {
  icon: Component
  iconBg: string
  value: number
  label: string
  subtext: string
}

export type SlotStatus = 'available' | 'reserved' | 'buffer' | 'maintenance' | 'out_of_service'

export interface ScheduleBlock {
  startMinutes: number
  endMinutes: number
  status: SlotStatus
  title: string
}

export interface ScheduleRow {
  id: string
  name: string
  subtitle: string
  icon: Component
  blocks: ScheduleBlock[]
  // Salle/équipement indisponible toute la journée (maintenance, hors service...) :
  // remplace les blocs individuels par une seule piste pleine largeur.
  fullDayStatus?: SlotStatus
  fullDayLabel?: string
}
