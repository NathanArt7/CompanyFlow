export interface RawNotificationData {
  title: string
  message: string
  ticket_id?: number
  equipment_id?: number
  reservation_id?: number
}

export interface RawNotification {
  id: string
  type: string
  data: RawNotificationData
  read_at: string | null
  created_at: string
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  total: number
  per_page: number
  last_page: number
}
