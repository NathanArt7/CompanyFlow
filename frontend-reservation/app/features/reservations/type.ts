export interface DayData {
  date: number
  dayName: string
  reservationCount: number
  bars: number[] // valeurs horaires simplifiées pour le mini-graphe
  isSelected: boolean
  isToday: boolean
}

import type { Component } from 'vue'

export interface SummaryCard {
  icon: Component
  iconBg: string
  value: number
  label: string
  barColor: string
  percentage: number
  percentageLabel: string
}

export type ReservationStatus = 'confirmed' | 'cancelled' | 'pending'
export type StatusDotColor = 'purple' | 'orange' | 'red'

export interface ReservationRow {
  id: string
  dotColor: StatusDotColor
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
  extraEquipmentCount?: number
}