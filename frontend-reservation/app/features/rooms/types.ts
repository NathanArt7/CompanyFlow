export type RoomType = 'reunion' | 'stockage'
export type RoomStatus = 'available' | 'occupied' | 'maintenance'

export interface RoomStat {
  icon: Component
  iconBg: string
  value: number
  label: string
  subtext: string
}

export interface Room {
  id: string
  name: string
  code: string
  type: RoomType
  capacity: number | null
  location: string
  status: RoomStatus
}