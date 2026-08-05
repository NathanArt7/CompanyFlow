export const WEEK_DAYS = [
  'MONDAY',
  'TUESDAY',
  'WEDNESDAY',
  'THURSDAY',
  'FRIDAY',
  'SATURDAY',
  'SUNDAY',
] as const

export type DayOfWeek = typeof WEEK_DAYS[number]

export interface RawServiceHour {
  id: number
  reservation_setting_id: number
  day_of_week: DayOfWeek
  is_open: boolean
  start_time: string | null
  end_time: string | null
}

export interface RawReservationSetting {
  id: number
  entreprise_id: number
  reservation_buffer: number
  service_hours: RawServiceHour[]
}

export interface ServiceHourPayload {
  day_of_week: DayOfWeek
  is_open: boolean
  start_time: string | null
  end_time: string | null
}

export interface ReservationSettingPayload {
  reservation_buffer: number
  service_hours: ServiceHourPayload[]
}

export interface NotificationPreferences {
  reservations: boolean
  rappels: boolean
  systeme: boolean
  rapports_hebdomadaires: boolean
}

export interface RawEntreprise {
  id: number
  reference: string
  nom: string
  email: string | null
  telephone: string
  adresse: string
  site_web: string | null
  logo: string | null
  statut: string
  created_at: string
  updated_at: string
}

export interface UpdateEntreprisePayload {
  nom: string
  telephone: string
  adresse: string
  site_web?: string | null
}
