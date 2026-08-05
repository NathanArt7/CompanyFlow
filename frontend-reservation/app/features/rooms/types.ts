import type { Component } from 'vue'

export type RoomType = 'reunion' | 'stockage'
export type RoomStatus = 'available' | 'occupied' | 'maintenance' | 'out_of_service'

export interface RoomStat {
  icon: Component
  iconBg: string
  value: number
  label: string
  subtext: string
  barColor: string
  percentage: number
}

export interface Room {
  id: string
  name: string
  code: string
  type: RoomType
  capacity: number | null
  location: string
  description: string | null
  status: RoomStatus
}