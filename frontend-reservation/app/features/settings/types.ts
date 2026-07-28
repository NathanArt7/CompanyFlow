import type { Component } from 'vue'

export interface SettingsTab {
  id: string
  icon: Component
  label: string
  subtitle: string
}

export interface BusinessHourRow {
  day: string
  hours: string
  isClosed: boolean
}

export interface NotificationToggle {
  label: string
  description: string
  enabled: boolean
}