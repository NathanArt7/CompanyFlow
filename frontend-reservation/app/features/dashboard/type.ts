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