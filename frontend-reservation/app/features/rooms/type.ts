export interface RawRoom {
  id: number
  nom: string
  code: string
  type: { value: string, label: string }
  capacite: number | null
  localisation: string
  description: string | null
  statut: { value: string, label: string }
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  total: number
  per_page: number
  last_page: number
}

// RoomController::index() renvoie une AnonymousResourceCollection (RoomResource::collection())
// wrappant un paginator : la pagination est imbriquée sous "meta", contrairement à
// /reservations ou /dashboard/* qui renvoient le paginator brut à plat.
export interface RoomsResourceCollectionResponse<T> {
  data: T[]
  meta: {
    current_page: number
    total: number
    per_page: number
    last_page: number
  }
}

export interface RoomStats {
  total: number
  meeting_rooms: number
  storage_rooms: number
  meeting_rooms_occupied: number
  in_maintenance: number
}

export interface CreateRoomPayload {
  nom: string
  type: 'MEETING' | 'STORAGE'
  code: string
  capacite: number | null
  localisation: string
  description?: string | null
  statut: 'Disponible' | 'Occupée' | 'En maintenance' | 'Hors service'
}

export interface RoomFilters {
  search: string
  type: 'MEETING' | 'STORAGE' | null
  statut: 'Disponible' | 'Occupée' | 'En maintenance' | 'Hors service' | null
}
