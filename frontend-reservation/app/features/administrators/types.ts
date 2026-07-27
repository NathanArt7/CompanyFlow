import type { Component } from 'vue'

export type AdminRole = 'super_admin' | 'admin'
export type AdminStatus = 'active' | 'pending' | 'suspended'

export interface AdminStat {
  icon: Component
  iconBg: string
  value: number
  label: string
  subtext: string
  barColor: string
  percentage: number
}

export interface Administrator {
  id: string
  name: string
  email: string
  initials: string
  avatarBg: string
  role: AdminRole
  status: AdminStatus
  lastLogin: string
  createdAt: string
}