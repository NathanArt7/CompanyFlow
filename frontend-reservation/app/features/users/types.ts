import type { Component } from 'vue'

export type UserRole = 'super_admin' | 'admin' | 'super_employee' | 'employee' | 'technician'
export type UserStatus = 'active' | 'disabled'

export interface UserStat {
  icon: Component
  iconBg: string
  value: number
  label: string
  subtext: string
  barColor: string
  percentage: number
}

export interface AppUser {
  id: string
  name: string
  email: string
  initials: string
  avatarBg: string
  role: UserRole
  status: UserStatus
  createdAt: string
}
