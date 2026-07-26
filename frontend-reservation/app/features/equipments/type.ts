import type { Component } from 'vue'

export type EquipmentUsageType = 'borrowable' | 'fixed'
export type EquipmentState = 'available' | 'occupied' | 'maintenance' | 'functional' | 'broken' | 'out_of_service'

export interface EquipmentStat {
  icon: Component
  iconBg: string
  value: number
  label: string
  subtext: string
  barColor: string
  percentage: number
}

export interface Equipment {
  id: string
  name: string
  code: string
  category: string
  usageType: EquipmentUsageType
  location: string
  state: EquipmentState
  assignedTo: string | null
}