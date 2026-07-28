export const WEEK_DAYS = [
  'MONDAY',
  'TUESDAY',
  'WEDNESDAY',
  'THURSDAY',
  'FRIDAY',
  'SATURDAY',
  'SUNDAY',
] as const

export interface ServiceHourPayload {
  day_of_week: typeof WEEK_DAYS[number]
  is_open: boolean
  start_time: string
  end_time: string
}

export interface ReservationSettingsPayload {
  reservation_buffer: number
  service_hours: ServiceHourPayload[]
}
